<?php
/**
 * Template part for displaying events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

// ACF Fields
$event_start_date = get_field( 'epo__start-date' ) ?? null;
$event_end_date   = get_field( 'epo__end-date' ) ?? null;
$event_location   = get_field( 'epo__location' ) ?? null;

// Form
$event_form_title     = get_field( 'epo__form-title' ) ?? null;
$event_form_shortcode = get_field( 'epo__form-shortcode' ) ?? null;
$event_over_message   = get_field( 'epo__message-event-over' ) ?? null;

// Get event_types taxonomy terms for the current event
$event_types     = get_the_terms( get_the_ID(), 'event_types' );
$event_type_slug = '';
$is_webinar      = false;
$is_event        = false;

if ( $event_types && ! is_wp_error( $event_types ) ) {
    // Get the first event type slug
    $event_type_slug = $event_types[0]->slug;
    $is_webinar      = $event_type_slug === 'webinars';
    $is_event        = $event_type_slug === 'events';
}

// Build entry-content class with modifier
$entry_content_class = 'entry-content';
if ( ! empty( $event_type_slug ) ) {
    $entry_content_class .= ' entry-content--' . esc_attr( $event_type_slug );
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="<?= $entry_content_class ?>">

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

                <figure class="entry-content__thumbnail">
                    <?php
                    the_post_thumbnail( 'full', [ 'class' => '', 'title' => get_the_title() ] );
                    ?>
                </figure>

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

        <?php
        // Only show form section for "events" type (not webinars)
        if ( $is_event && $event_form_shortcode ) : ?>
            <div class="entry-content__the-form">
                <?php
                if ( $event_form_title ) : ?>
                    <h3 class="h4"><?= wp_kses_post( $event_form_title ) ?></h3>
                <?php
                endif; ?>

                <?= do_shortcode( $event_form_shortcode ); ?>
            </div>
        <?php endif; ?>

        <!-- Widget & Socials -->
        <div class="entry-content__sidebar-right">
            <div class="sticky-sidebar">
                <?php
                // Display post content sidebar widget if active
                if ( is_active_sidebar( 'post-content-sidebar' ) ) :
                    dynamic_sidebar( 'post-content-sidebar' );
                endif;

                // Social share buttons
                get_template_part( 'template-parts/components/post-social-share' );
                ?>
            </div>
        </div><!-- .entry-content__sidebar-right -->
    </div><!-- .entry-content -->

    <?php
    // Related webinars section - only for webinars event type
    if ( $is_webinar ) :
        // Get current event's event_types terms
        $current_event_types = wp_get_post_terms( get_the_ID(), 'event_types', [ 'fields' => 'ids' ] );

        // Only show related webinars if current event has event_types
        if ( ! empty( $current_event_types ) && ! is_wp_error( $current_event_types ) ) :
            // Query for related webinars
            $related_query = new WP_Query( [
                    'post_type'           => 'events',
                    'post_status'         => 'publish',
                    'posts_per_page'      => 2,
                    'post__not_in'        => [ get_the_ID() ], // Exclude current event
                    'tax_query'           => [
                            [
                                    'taxonomy' => 'event_types',
                                    'field'    => 'term_id',
                                    'terms'    => $current_event_types,
                            ],
                    ],
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                    'ignore_sticky_posts' => true,
            ] );

            // Only display section if we have related webinars
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

                        <!-- Related Webinars Grid -->
                        <div class="entry-related__grid">
                            <?php
                            while ($related_query->have_posts()) :
                                $related_query->the_post();
                                get_template_part( 'template-parts/components/related-webinar-item' );
                            endwhile;
                            wp_reset_postdata(); // Restore global post data
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            endif;
        endif;
    endif;
    ?>

    <?php
    // Post Footer Widget Area
    if ( is_active_sidebar( 'post-footer-widget' ) ) : ?>
        <div class="entry-footer">
            <div class="entry-footer__wrapper">
                <?php dynamic_sidebar( 'event-footer-widget' ); ?>
            </div>
        </div>
    <?php
    endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
