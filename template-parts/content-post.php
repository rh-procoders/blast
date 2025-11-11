<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

// Get TOC from Global
global $single_toc;

?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-content">

        <div class="entry-content__sidebar-left">
            <div class="sticky-sidebar">
                <?php
                if ( $single_toc ) : ?>
                    <div class="bs-toc">
                            <span class="bs-toc__heading">
                                <?php echo __( "Intro", 'blast-2025' ) ?>
                            </span>

                        <div class="bs-toc__container">
                            <?php
                            echo $single_toc; ?>
                        </div>
                    </div>
                <?php
                endif; ?>
            </div>
        </div><!-- .entry-content__sidebar-left -->

        <div class="entry-content__the-content">

            <!-- Back to Blog -->
            <a class="entry-content__backlink"
               href="<?= get_post_type_archive_link( 'post' ) ?>">
                <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>

                <?= __( "Back to Blog", 'blast-2025' ) ?>
            </a>

            <div class="entry-content__wrapper">

                <?php
                the_title( '<h1 class="entry-content__title">', '</h1>' );
                ?>

                <div class="entry-content__excerpt">
                    <?php the_excerpt(); ?>
                </div>

                <div class="entry-content__meta">
                    <?php
                    get_template_part( 'template-parts/components/author', null, [
                            'user_id'     => get_the_author_meta( 'ID' ),
                            'avatar_size' => 50,
                            'inline'      => true
                    ] );
                    ?>

                    <span class="post-date">
                        <?= esc_html( get_the_date( 'jS M, Y' ) ) ?>
                    </span>
                </div>

                <?php
                the_post_thumbnail( 'post-thumbnail', [ 'class' => 'entry-content__thumbnail', 'title' => get_the_title() ] );
                ?>

                <?php
                the_content(
                        sprintf(
                                wp_kses(
                                /* translators: %s: Name of current post. Only visible to screen readers */
                                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'blast-2025' ),
                                        array(
                                                'span' => array(
                                                        'class' => array(),
                                                ),
                                        )
                                ),
                                wp_kses_post( get_the_title() )
                        )
                );

                ?>
            </div>
        </div><!-- .entry-content__the-content -->

        <!-- Widget & Socials -->
        <div class="entry-content__sidebar-right">
            <div class="sticky-sidebar">
                social shares and widget
            </div>
        </div><!-- .entry-content__sidebar-right -->
    </div><!-- .entry-content -->

    <div class="entry-author">
        <div class="entry-author__wrapper">
            <?php
            get_template_part( 'template-parts/components/author', null, [
                    'user_id'           => get_the_author_meta( 'ID' ),
                    'avatar_size'       => 150,
                    'inline'            => true,
                    'show_job_position' => true,
                    'show_bio'          => true
            ] );
            ?>
        </div>
    </div>

    <?php
    // Get current post's categories
    $current_categories = wp_get_post_categories( get_the_ID() );

    // Only show related posts if current post has categories
    if ( ! empty( $current_categories ) ) :
        // Query for related posts
        $related_query = new WP_Query( [
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 2,
                'post__not_in'        => [ get_the_ID() ], // Exclude current post
                'category__in'        => $current_categories,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
        ] );

        // Only display section if we have related posts
        if ( $related_query->have_posts() ) : ?>
            <div class="entry-related">
                <div class="entry-related__wrapper">
                    <!-- Section Heading -->
                    <h2 class="entry-related__heading">
                        <?php
                        /* translators: "like" should be emphasized/styled */
                        echo wp_kses_post( __( 'You might also <strong>like</strong>', 'blast-2025' ) );
                        ?>
                    </h2>

                    <!-- Related Posts Grid -->
                    <div class="entry-related__grid">
                        <?php
                        while ($related_query->have_posts()) :
                            $related_query->the_post();
                            get_template_part( 'template-parts/components/related-post-item' );
                        endwhile;
                        wp_reset_postdata(); // Restore global post data
                        ?>
                    </div>
                </div>
            </div>
        <?php
        endif;
    endif;
    ?>

</article><!-- #post-<?php the_ID(); ?> -->
