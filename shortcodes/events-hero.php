<?php
declare(strict_types=1);

/**
 * Events Hero Shortcode
 * Displays the latest event (no slider, single event display)
 *
 * @package blast_Wp
 */

/**
 * Shortcode: [events-hero]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function blast_events_hero_shortcode( array $atts ): string
{
    // Query the latest event
    $args = [
            'post_type'      => 'events',
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'posts_per_page' => 1,
            'meta_query'     => [
                    'relation' => 'OR',
                    [
                            'key'     => 'epo__event-unlisted',
                            'compare' => 'NOT EXISTS',
                    ],
                    [
                            'key'     => 'epo__event-unlisted',
                            'value'   => '1',
                            'compare' => '!=',
                    ],
            ],
    ];

    $events_query = new WP_Query( $args );

    // If no events found, return empty
    if ( ! $events_query->have_posts() ) {
        return '';
    }

    ob_start();

    // Get the single event
    $events_query->the_post();

    $post_id        = get_the_ID();
    $post_title     = get_the_title();
    $post_excerpt   = get_the_excerpt();
    $post_permalink = get_permalink();

    $event_banner_hero = get_field( 'epo__banner-hero' ) ?? null;

    // Priority: Custom ACF banner image > Featured image
    if ( $event_banner_hero && ! empty( $event_banner_hero['ID'] ) ) {
        $post_thumbnail = wp_get_attachment_image( $event_banner_hero['ID'], 'large', false, [
            'class' => 'archive-hero__thumbnail-image',
        ] );
    } else {
        $post_thumbnail = get_the_post_thumbnail( $post_id, 'large', [
            'class' => 'archive-hero__thumbnail-image',
        ] );
    }

    $event_start_date = get_field( 'epo__start-date' ) ?? null;
    $event_end_date   = get_field( 'epo__end-date' ) ?? null;
    $event_location   = get_field( 'epo__location' ) ?? null;
    $event_hero_label = get_field( 'epo__hero-label' ) ?? null;
    ?>

    <div class="archive-hero">
        <div class="archive-hero__wrapper">

            <!-- Content (Left) -->
            <div class="archive-hero__content">
                <div class="archive-hero__single-content">
                    <div class="archive-hero__slide-content">
                        <div class="archive-hero__featured-badge">
                            <?php sprite_svg( 'icon-calendar-1', 16, 16 ); ?>
                            <span>
                                <?php if ( $event_hero_label ) {
                                    echo wp_kses_post( $event_hero_label );
                                } else {
                                    echo esc_html( __( 'Latest', 'blast-2025' ) );
                                } ?>
                            </span>
                        </div>

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

                        <?php
                        if ( $event_start_date || $event_end_date || $event_location ) : ?>
                            <?= blast_format_event_meta(
                                    $event_start_date,
                                    $event_end_date,
                                    $event_location
                            ); ?>
                        <?php
                        endif; ?>

                        <a class="btn has-coral-background-color"
                           href="<?= esc_url( $post_permalink ) ?>">
                            <span class="button-text"
                                  data-hover-text="<?= __( 'Register', 'blast-2025' ); ?>">
                                <?= __( 'Register', 'blast-2025' ); ?>
                            </span>

                            <span class="button-arrow-wrapper">
                                <?php sprite_svg( 'icon-arrow-right', 14, 10 ); ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thumbnail (Right) -->
            <div class="archive-hero__thumbnails">
                <a href="<?= esc_url( $post_permalink ) ?>"
                   class="archive-hero__thumbnail archive-hero__thumbnail--single">
                    <?php
                    if ( $post_thumbnail ) :
                        echo $post_thumbnail;
                    else :
                        ?>
                        <div class="archive-hero__thumbnail-placeholder">
                            <!-- Placeholder for events without featured image -->
                        </div>
                    <?php
                    endif;
                    ?>
                </a>
            </div>

        </div><!-- /.archive-hero__wrapper -->
    </div><!-- /.archive-hero -->

    <?php
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode( 'events-hero', 'blast_events_hero_shortcode' );
