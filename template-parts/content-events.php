<?php
/**
 * Template part for displaying events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

// Date & Location
$event_start_date = get_field( 'epo__start-date' ) ?? null;
$event_end_date   = get_field( 'epo__end-date' ) ?? null;
$event_location   = get_field( 'epo__location' ) ?? null;

// Event Type & running or completed
$event_type         = get_field( 'epo__event-type' ) ?? 'event'; // event || webinar
$event_is_completed = get_field( 'epo__event-completed' ) ?? FALSE;

// Form
$event_form_title     = get_field( 'epo__form-title' ) ?? null;
$event_form_shortcode = get_field( 'epo__form-shortcode' ) ?? null;

// Custom Fields if event is over
$event_over_message      = get_field( 'epo__message-event-over' ) ?? null;
$webinar_video_source    = get_field( 'epo__video-source' ) ?? null;
$webinar_video_id        = get_field( 'epo__video-id' ) ?? null;
$webinar_video_thumbnail = get_field( 'epo__video-thumbnail' ) ?? null;
$webinar_related         = get_field( 'epo__related-webinars' ) ?? null;

// Build entry-content class with status and type modifiers
$status_modifier     = $event_is_completed ? 'event-completed' : 'event-ongoing';
$type_modifier       = 'type-' . $event_type;
$entry_content_class = 'entry-content entry-content--' . $status_modifier . ' entry-content--' . $type_modifier;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="<?= $entry_content_class ?>">

        <div class="entry-content__the-content">

            <!-- Back to Events -->
            <a class="entry-content__backlink"
               href="<?= esc_url( home_url( '/events/' ) ) ?>">
                <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>

                <?= __( "Back to Events", 'blast-2025' ) ?>
            </a>

            <div class="entry-content__wrapper">
                <?php
                // Only show date & location if event is ongoing
                if ( ! $event_is_completed && ($event_start_date || $event_end_date || $event_location) ) : ?>
                    <div class="entry-content__meta">
                        <?= blast_format_event_meta(
                                $event_start_date,
                                $event_end_date,
                                $event_location
                        ); ?>
                    </div>
                <?php
                endif; ?>

                <?php
                the_title( '<h1 class="entry-content__title">', '</h1>' );
                ?>

                <?php
                // Show video preview for completed webinars, regular thumbnail otherwise
                if ( $event_is_completed && $event_type === 'webinar' && $webinar_video_source && $webinar_video_id ) : ?>
                    <div class="entry-content__video-preview"
                         data-video-source="<?= esc_attr( $webinar_video_source ) ?>"
                         data-video-id="<?= esc_attr( $webinar_video_id ) ?>">
                        <?php if ( $webinar_video_thumbnail ) : ?>
                            <img class=""
                                 src="<?php echo esc_url( $webinar_video_thumbnail['url'] ); ?>"
                                 alt="<?php echo esc_attr( $webinar_video_thumbnail['title'] ); ?>"
                                 loading="lazy">
                        <?php endif; ?>
                        <button class="entry-content__video-play"
                                aria-label="<?= esc_attr__( 'Play video', 'blast-2025' ) ?>">
                            <?php sprite_svg( 'icon-play', 640, 640 ) ?>
                        </button>
                        <div class="entry-content__video-container"></div>
                    </div>
                <?php elseif ( ! ($event_is_completed && $event_type === 'webinar') ) : ?>
                    <figure class="entry-content__thumbnail">
                        <?php
                        the_post_thumbnail( 'full', [ 'class' => '', 'title' => get_the_title() ] );
                        ?>
                    </figure>
                <?php endif; ?>

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

            <?php
            // Related webinars section - show only if event is completed AND type is webinar AND manually selected
            if ( $event_is_completed && $event_type === 'webinar' && ! empty( $webinar_related ) && is_array( $webinar_related ) ) :
                // Limit to first 3 post IDs
                $related_ids = array_slice( $webinar_related, 0, 3 );

                // Query for manually selected related webinars
                $related_query = new WP_Query( [
                        'post_type'           => 'events',
                        'post_status'         => 'publish',
                        'post__in'            => $related_ids,
                        'orderby'             => 'post__in',
                        'posts_per_page'      => 3,
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
                                echo wp_kses_post( __( 'Watch next', 'blast-2025' ) );
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
            endif; ?>
        </div><!-- .entry-content__the-content -->

        <?php
        // Hide form area if event is completed AND type is webinar
        if ( $event_form_shortcode && ! ($event_is_completed && $event_type === 'webinar') ) : ?>
            <div class="entry-content__the-form">
                <div class="sticky-sidebar">
                    <?php
                    if ( $event_form_title ) : ?>
                        <h3 class="h4"><?= wp_kses_post( $event_form_title ) ?></h3>
                    <?php
                    endif; ?>

                    <?php if ( ! $event_is_completed ) : ?>
                        <?= do_shortcode( $event_form_shortcode ); ?>
                    <?php else : ?>
                        <?php if ( $event_over_message ) : ?>
                            <div class="event-over-message">
                                <?= wp_kses_post( $event_over_message ) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
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
    // Add error state handling for events form when form is displayed (only for ongoing events/webinars)
    if ( $event_form_shortcode && ! $event_is_completed ) : ?>
        <script>
            (function () {
                'use strict';

                /**
                 * CF7 Button Error State Handler for Events Form
                 * Adds error class to submit button when validation fails
                 * Scoped to forms inside .entry-content__the-form only
                 */
                function initEventFormErrorStates() {
                    const formContainer = document.querySelector( '.entry-content__the-form' );

                    if (!formContainer) {
                        return;
                    }

                    const form = formContainer.querySelector( 'form.wpcf7-form' );

                    if (!form) {
                        return;
                    }

                    /**
                     * Add error class to the submit button
                     */
                    function addErrorClassToButton() {
                        const submitButton = form.querySelector( '.wpcf7-submit' );
                        if (submitButton && !submitButton.classList.contains( 'has-error' )) {
                            submitButton.classList.add( 'has-error' );
                        }
                    }

                    /**
                     * Remove error class from submit button
                     */
                    function removeErrorClassFromButton() {
                        const submitButton = form.querySelector( '.wpcf7-submit.has-error' );
                        if (submitButton) {
                            submitButton.classList.remove( 'has-error' );
                        }
                    }

                    // Method 1: Listen for CF7 validation error event
                    document.addEventListener( 'wpcf7invalid', function ( event ) {
                        if (event.target === form) {
                            addErrorClassToButton();
                        }
                    }, false );

                    // Method 2: Watch for validation error message appearing (MutationObserver)
                    const observer = new MutationObserver( function ( mutations ) {
                        mutations.forEach( function ( mutation ) {
                            // Check if validation errors div became visible
                            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                                const target = mutation.target;
                                if (target.classList.contains( 'wpcf7-validation-errors' )) {
                                    const isVisible = target.style.display !== 'none' && target.style.display !== '';
                                    if (isVisible) {
                                        addErrorClassToButton();
                                    }
                                }
                            }
                        } );
                    } );

                    // Observe all validation error containers
                    const errorContainers = form.querySelectorAll( '.wpcf7-response-output' );
                    errorContainers.forEach( container => {
                        observer.observe( container, {
                            attributes: true,
                            attributeFilter: ['style', 'class']
                        } );
                    } );

                    // Method 3: Watch for "sending" class removal, then check for errors
                    form.addEventListener( 'click', function ( event ) {
                        const clickedButton = event.target.closest( '.wpcf7-submit' );

                        if (clickedButton) {
                            // Remove error class when user clicks (they're trying again)
                            if (clickedButton.classList.contains( 'has-error' )) {
                                clickedButton.classList.remove( 'has-error' );
                            }

                            // Watch for when "sending" class is removed (validation complete)
                            const buttonObserver = new MutationObserver( function ( mutations ) {
                                mutations.forEach( function ( mutation ) {
                                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                        const button = mutation.target;

                                        // Check if "sending" class was just removed
                                        if (!button.classList.contains( 'sending' )) {
                                            // Small delay to ensure DOM is updated
                                            setTimeout( function () {
                                                // Check for validation errors
                                                const errorMessage = form.querySelector( '.wpcf7-response-output.wpcf7-validation-errors' );
                                                const hasVisibleErrorMessage = errorMessage && errorMessage.offsetParent !== null;
                                                const hasInvalidFields = form.querySelector( '.wpcf7-not-valid' ) !== null;
                                                const hasErrorTips = form.querySelector( '.wpcf7-not-valid-tip' ) !== null;

                                                if (hasVisibleErrorMessage || hasInvalidFields || hasErrorTips) {
                                                    addErrorClassToButton();
                                                }

                                                // Stop observing after check
                                                buttonObserver.disconnect();
                                            }, 50 );
                                        }
                                    }
                                } );
                            } );

                            // Start observing the button for class changes
                            buttonObserver.observe( clickedButton, {
                                attributes: true,
                                attributeFilter: ['class']
                            } );
                        }
                    } );

                    // Remove error class on successful form submission
                    document.addEventListener( 'wpcf7mailsent', function ( event ) {
                        if (event.target === form) {
                            removeErrorClassFromButton();
                        }
                    }, false );
                }

                // Initialize when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener( 'DOMContentLoaded', initEventFormErrorStates );
                } else {
                    // DOM already loaded
                    initEventFormErrorStates();
                }
            })();
        </script>

        <script id="lp-cf7-redirect">
            document.addEventListener( 'wpcf7mailsent', function ( event ) {
                // Only redirect if the submitted form is inside .entry-content__the-form
                const formContainer = event.target.closest( '.entry-content__the-form' );

                if (formContainer) {
                    setTimeout( () => {
                        location = '<?= esc_url( home_url( '/thank-you/' ) ) ?>';
                    }, 0 );
                }
            }, false );
        </script>
    <?php endif; ?>

    <?php
    // Add video embed functionality for completed webinars
    if ( $event_is_completed && $event_type === 'webinar' && $webinar_video_source && $webinar_video_id ) : ?>
        <script>
            (function () {
                'use strict';

                /**
                 * Webinar Video Player
                 * Handles inline video playback for completed webinars
                 */
                function initWebinarVideoPlayer() {
                    const videoPreview = document.querySelector( '.entry-content__video-preview' );

                    if (!videoPreview) {
                        return;
                    }

                    const source = videoPreview.dataset.videoSource;
                    const id = videoPreview.dataset.videoId;
                    const thumbnail = videoPreview.querySelector( '.entry-content__video-thumbnail' );
                    const playButton = videoPreview.querySelector( '.entry-content__video-play' );
                    const videoContainer = videoPreview.querySelector( '.entry-content__video-container' );

                    if (!source || !id || !videoContainer) {
                        console.error( 'Missing video data' );
                        return;
                    }

                    // Handle click on the entire video preview area
                    videoPreview.addEventListener( 'click', function ( event ) {
                        event.preventDefault();

                        // Prevent double-loading
                        if (videoContainer.querySelector( 'iframe' )) {
                            return;
                        }

                        // Add class to trigger CSS transitions
                        videoPreview.classList.add( 'video-started' );

                        // Wait for fade animation, then create and inject iframe
                        setTimeout( function () {
                            // Hide thumbnail and play button completely
                            if (thumbnail) {
                                thumbnail.style.display = 'none';
                            }
                            if (playButton) {
                                playButton.style.display = 'none';
                            }

                            // Create iframe
                            const iframe = document.createElement( 'iframe' );
                            iframe.width = '560';
                            iframe.height = '315';
                            iframe.frameBorder = '0';
                            iframe.allow = 'autoplay; fullscreen; picture-in-picture';
                            iframe.allowFullscreen = true;
                            iframe.style.width = '100%';
                            iframe.style.height = '100%';
                            iframe.style.position = 'absolute';
                            iframe.style.top = '0';
                            iframe.style.left = '0';

                            // Set iframe source based on video platform
                            if (source === 'youtube') {
                                iframe.id = `yt_${id}`;
                                iframe.title = 'YouTube video player';
                                iframe.src = `https://www.youtube-nocookie.com/embed/${id}?rel=0&color=white&autoplay=1`;
                            } else if (source === 'vimeo') {
                                iframe.id = `vimeo_${id}`;
                                iframe.title = 'Vimeo video player';
                                iframe.src = `https://player.vimeo.com/video/${id}?autoplay=1&title=0&byline=0&portrait=0`;
                            } else {
                                console.error( 'Unsupported video source:', source );
                                return;
                            }

                            // Append iframe to video container
                            videoContainer.appendChild( iframe );
                        }, 300 );
                    } );
                }

                // Initialize when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener( 'DOMContentLoaded', initWebinarVideoPlayer );
                } else {
                    // DOM already loaded
                    initWebinarVideoPlayer();
                }
            })();
        </script>
    <?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
