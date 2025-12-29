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

            <!-- Back to Events -->
            <a class="entry-content__backlink"
               href="<?= get_post_type_archive_link( 'post' ) ?>">
                <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>

                <?= __( "Back to Events", 'blast-2025' ) ?>
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
                <div class="sticky-sidebar">
                    <?php
                    if ( $event_form_title ) : ?>
                        <h3 class="h4"><?= wp_kses_post( $event_form_title ) ?></h3>
                    <?php
                    endif; ?>

                    <?= do_shortcode( $event_form_shortcode ); ?>
                </div>
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

    <?php
    // Add error state handling for events form (only for event_type === 'events')
    if ( $is_event && $event_form_shortcode ) : ?>
        <script>
        (function() {
            'use strict';

            /**
             * CF7 Button Error State Handler for Events Form
             * Adds error class to submit button when validation fails
             * Scoped to forms inside .entry-content__the-form only
             */
            function initEventFormErrorStates() {
                const formContainer = document.querySelector('.entry-content__the-form');

                if (!formContainer) {
                    return;
                }

                const form = formContainer.querySelector('form.wpcf7-form');

                if (!form) {
                    return;
                }

                /**
                 * Add error class to the submit button
                 */
                function addErrorClassToButton() {
                    const submitButton = form.querySelector('.wpcf7-submit');
                    if (submitButton && !submitButton.classList.contains('has-error')) {
                        submitButton.classList.add('has-error');
                    }
                }

                /**
                 * Remove error class from submit button
                 */
                function removeErrorClassFromButton() {
                    const submitButton = form.querySelector('.wpcf7-submit.has-error');
                    if (submitButton) {
                        submitButton.classList.remove('has-error');
                    }
                }

                // Method 1: Listen for CF7 validation error event
                document.addEventListener('wpcf7invalid', function(event) {
                    if (event.target === form) {
                        addErrorClassToButton();
                    }
                }, false);

                // Method 2: Watch for validation error message appearing (MutationObserver)
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        // Check if validation errors div became visible
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            const target = mutation.target;
                            if (target.classList.contains('wpcf7-validation-errors')) {
                                const isVisible = target.style.display !== 'none' && target.style.display !== '';
                                if (isVisible) {
                                    addErrorClassToButton();
                                }
                            }
                        }
                    });
                });

                // Observe all validation error containers
                const errorContainers = form.querySelectorAll('.wpcf7-response-output');
                errorContainers.forEach(container => {
                    observer.observe(container, {
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                });

                // Method 3: Watch for "sending" class removal, then check for errors
                form.addEventListener('click', function(event) {
                    const clickedButton = event.target.closest('.wpcf7-submit');

                    if (clickedButton) {
                        // Remove error class when user clicks (they're trying again)
                        if (clickedButton.classList.contains('has-error')) {
                            clickedButton.classList.remove('has-error');
                        }

                        // Watch for when "sending" class is removed (validation complete)
                        const buttonObserver = new MutationObserver(function(mutations) {
                            mutations.forEach(function(mutation) {
                                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                    const button = mutation.target;

                                    // Check if "sending" class was just removed
                                    if (!button.classList.contains('sending')) {
                                        // Small delay to ensure DOM is updated
                                        setTimeout(function() {
                                            // Check for validation errors
                                            const errorMessage = form.querySelector('.wpcf7-response-output.wpcf7-validation-errors');
                                            const hasVisibleErrorMessage = errorMessage && errorMessage.offsetParent !== null;
                                            const hasInvalidFields = form.querySelector('.wpcf7-not-valid') !== null;
                                            const hasErrorTips = form.querySelector('.wpcf7-not-valid-tip') !== null;

                                            if (hasVisibleErrorMessage || hasInvalidFields || hasErrorTips) {
                                                addErrorClassToButton();
                                            }

                                            // Stop observing after check
                                            buttonObserver.disconnect();
                                        }, 50);
                                    }
                                }
                            });
                        });

                        // Start observing the button for class changes
                        buttonObserver.observe(clickedButton, {
                            attributes: true,
                            attributeFilter: ['class']
                        });
                    }
                });

                // Remove error class on successful form submission
                document.addEventListener('wpcf7mailsent', function(event) {
                    if (event.target === form) {
                        removeErrorClassFromButton();
                    }
                }, false);
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initEventFormErrorStates);
            } else {
                // DOM already loaded
                initEventFormErrorStates();
            }
        })();
        </script>
    <?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
