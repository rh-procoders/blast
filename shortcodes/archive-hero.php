<?php
declare(strict_types=1);

/**
 * Archive Hero Shortcode
 * Displays featured posts in a synced Splide slider (content + thumbnails)
 *
 * @package blast-2025
 */

/**
 * Shortcode: [blast-archive-hero]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 *
 * @throws Exception
 */
function blast_archive_hero_shortcode( array $atts ): string
{
    // Parse shortcode attributes
    $atts = shortcode_atts( [
            'tax'    => '',      // Taxonomy type: category, tag, author (empty for blog)
            'tax_id' => null,    // Specific taxonomy ID to filter (for archives)
    ], $atts, 'blast-archive-hero' );

    $taxonomy = ! empty( $atts['tax'] ) ? sanitize_key( $atts['tax'] ) : '';
    $tax_id   = ! empty( $atts['tax_id'] ) ? absint( $atts['tax_id'] ) : null;

    // Build query args for featured posts
    $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'posts_per_page' => -1, // No limit for featured posts
            'meta_query'     => [
                    [
                            'key'     => BS_FEATURED_META_KEY,
                            'value'   => '1',
                            'compare' => '=',
                    ],
            ],
    ];

    // Add taxonomy filter if in archive mode
    if ( $tax_id && $taxonomy ) {
        if ( $taxonomy === 'category' ) {
            $args['category__in'] = [ $tax_id ];
        } elseif ( $taxonomy === 'tag' ) {
            $args['tag_id'] = $tax_id;
        } elseif ( $taxonomy === 'author' ) {
            $args['author'] = $tax_id;
        }
    }

    // Query featured posts
    $featured_query = new WP_Query( $args );

    // If no featured posts found, get latest 3 posts
    if ( ! $featured_query->have_posts() ) {
        $args['posts_per_page'] = 3;
        unset( $args['meta_query'] ); // Remove featured filter

        $featured_query = new WP_Query( $args );
    }

    // If still no posts, return empty
    if ( ! $featured_query->have_posts() ) {
        return '';
    }

    // Generate unique ID for this slider instance
    $slider_id = 'archive-hero-' . uniqid();

    ob_start();
    ?>

    <div class="container container--blog-filter">
        <div class="archive-hero">
            <div class="archive-hero__wrapper">

                <!-- Content Slider (Left) -->
                <div class="archive-hero__content">
                    <div id="<?= esc_attr( $slider_id . '-content' ) ?>"
                         class="splide archive-hero__splide archive-hero__splide--content">
                        <div class="splide__track archive-hero__splide-track">
                            <ul class="splide__list archive-hero__splide-list">
                                <?php
                                while ($featured_query->have_posts()) :
                                    $featured_query->the_post();

                                    // Get post data
                                    $post_id        = get_the_ID();
                                    $post_title     = get_the_title();
                                    $post_excerpt   = get_the_excerpt();
                                    $post_permalink = get_permalink();
                                    $is_featured    = get_post_meta( $post_id, BS_FEATURED_META_KEY, true );
                                    ?>

                                    <li class="splide__slide archive-hero__splide-slide">
                                        <div class="archive-hero__slide-content">
                                            <?php
                                            // if ( $is_featured === '1' ) :
                                            ?>
                                            <div class="archive-hero__featured-badge">
                                                <?php sprite_svg( 'icon-archive-featured', 14, 13 ); ?>
                                                <span>
                                                <?= esc_html__( 'Featured', 'blast-2025' ) ?>
                                            </span>
                                            </div>
                                            <?php
                                            // endif;
                                            ?>

                                            <h2 class="archive-hero__title">
                                                <a href="<?= esc_url( $post_permalink ) ?>">
                                                    <?= esc_html( $post_title ) ?>
                                                </a>
                                            </h2>

                                            <?php if ( $post_excerpt ) : ?>
                                                <div class="archive-hero__excerpt">
                                                    <?= wp_kses_post( $post_excerpt ) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php
                                endwhile;
                                ?>
                            </ul>
                        </div>

                        <!-- Navigation (Arrows + Counter) -->
                        <div class="archive-hero__navigation">
                            <div class="splide__arrows archive-hero__arrows">
                                <button class="splide__arrow splide__arrow--prev archive-hero__arrow archive-hero__arrow--prev"
                                        type="button"
                                        aria-label="<?= esc_attr__( 'Previous slide', 'blast-2025' ) ?>">
                                    <?php sprite_svg( 'icon-archive-slider-arrow-right', 26, 26 ); ?>
                                </button>

                                <button class="splide__arrow splide__arrow--next archive-hero__arrow archive-hero__arrow--next"
                                        type="button"
                                        aria-label="<?= esc_attr__( 'Next slide', 'blast-2025' ) ?>">
                                    <?php sprite_svg( 'icon-archive-slider-arrow-right', 26, 26 ); ?>
                                </button>
                            </div>

                            <div class="archive-hero__counter">
                                <span class="archive-hero__counter-current">1</span>
                                <span class="archive-hero__counter-separator">/</span>
                                <span class="archive-hero__counter-total"><?= esc_html( $featured_query->post_count ) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail Slider (Right) -->
                <div class="archive-hero__thumbnails">
                    <div id="<?= esc_attr( $slider_id . '-thumbnails' ) ?>"
                         class="splide archive-hero__splide archive-hero__splide--thumbnails">
                        <div class="splide__track archive-hero__splide-track">
                            <ul class="splide__list archive-hero__splide-list">
                                <?php
                                // Reset query for thumbnails
                                $featured_query->rewind_posts();

                                while ($featured_query->have_posts()) :
                                    $featured_query->the_post();

                                    $post_thumbnail = get_the_post_thumbnail( get_the_ID(), 'large', [
                                            'class' => 'archive-hero__thumbnail-image',
                                    ] );
                                    ?>

                                    <li class="splide__slide archive-hero__splide-slide">
                                        <a href="<?= esc_url( get_permalink() ) ?>" class="archive-hero__thumbnail">
                                            <?php
                                            if ( $post_thumbnail ) :
                                                echo $post_thumbnail;
                                            else :
                                                ?>
                                                <div class="archive-hero__thumbnail-placeholder">
                                                    <!-- Placeholder for posts without featured image -->
                                                </div>
                                            <?php
                                            endif;
                                            ?>
                                        </a>
                                    </li>

                                <?php
                                endwhile;
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div><!-- /.archive-hero__wrapper -->
        </div>
    </div><!-- /.archive-hero -->

    <script id="archive-hero-scripts" type="text/javascript">
        document.addEventListener( 'DOMContentLoaded', function () {
            // Initialize thumbnail slider (fade effect)
            const thumbnailSlider = new Splide( '#<?= esc_js( $slider_id . '-thumbnails' ) ?>', {
                type: 'fade',
                rewind: true,
                pagination: false,
                arrows: false,
                drag: false,
            } );

            // Initialize content slider (slide effect)
            const contentSlider = new Splide( '#<?= esc_js( $slider_id . '-content' ) ?>', {
                type: 'loop',
                rewind: false,
                pagination: false,
                arrows: true,
            } );

            // Sync sliders
            contentSlider.sync( thumbnailSlider );

            // Mount both sliders
            thumbnailSlider.mount();
            contentSlider.mount();

            // Update counter on slide change
            contentSlider.on( 'move', function ( newIndex ) {
                const currentElement = document.querySelector( '#<?= esc_js( $slider_id . '-content' ) ?> .archive-hero__counter-current' );
                if (currentElement) {
                    currentElement.textContent = newIndex + 1;
                }
            } );
        } );
    </script>

    <?php
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode( 'blast-archive-hero', 'blast_archive_hero_shortcode' );
