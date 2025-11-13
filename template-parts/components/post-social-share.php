<?php
/**
 * Template part: Post Social Share
 * Displays social sharing buttons for LinkedIn, Email, and Copy to Clipboard
 *
 * @package blast-2025
 */

// Get current post data
$post_url   = esc_url( get_permalink() );
$post_title = esc_attr( get_the_title() );

// Build share URLs
$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode( $post_url );
$email_url    = 'mailto:?subject=' . rawurlencode( $post_title ) . '&body=' . rawurlencode( $post_url );
?>

<div class="post-social-share">
    <ul class="post-social-share__list">
        <!-- LinkedIn -->
        <li class="post-social-share__item">
            <a href="<?= $linkedin_url ?>"
               class="post-social-share__link post-social-share__link--linkedin"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="<?= esc_attr__( 'Share on LinkedIn', 'blast-2025' ) ?>">
				<?php sprite_svg('icon-share-linkedin', 16, 16) ?>
            </a>
        </li>

        <!-- Email -->
        <li class="post-social-share__item">
            <a href="<?= $email_url ?>"
               class="post-social-share__link post-social-share__link--email"
               aria-label="<?= esc_attr__( 'Share via Email', 'blast-2025' ) ?>">
                <?php sprite_svg('icon-share-mail', 22, 22) ?>
            </a>
        </li>

        <!-- Copy to Clipboard -->
        <li class="post-social-share__item">
            <button type="button"
                    class="post-social-share__link post-social-share__link--copy"
                    data-url="<?= $post_url ?>"
                    aria-label="<?= esc_attr__( 'Copy link to clipboard', 'blast-2025' ) ?>">
                <?php sprite_svg('icon-share-clipboard', 18, 18) ?>

                <span class="post-social-share__tooltip post-social-share__tooltip--hover">
					<?= esc_html__( 'Copy link', 'blast-2025' ) ?>
				</span>
                <span class="post-social-share__tooltip post-social-share__tooltip--copied">
					<?= esc_html__( 'Copied!', 'blast-2025' ) ?>
				</span>
            </button>
        </li>
    </ul>
</div>

<script>
    (function () {
        'use strict';

        document.addEventListener( 'DOMContentLoaded', function () {
            const copyButtons = document.querySelectorAll( '.post-social-share__link--copy' );

            copyButtons.forEach( function ( button ) {
                const hoverTooltip = button.querySelector( '.post-social-share__tooltip--hover' );
                const copiedTooltip = button.querySelector( '.post-social-share__tooltip--copied' );

                // Show "Copy link" on hover
                button.addEventListener( 'mouseenter', function () {
                    hoverTooltip.classList.add( 'post-social-share__tooltip--visible' );
                } );

                // Hide "Copy link" when mouse leaves (unless copying)
                button.addEventListener( 'mouseleave', function () {
                    hoverTooltip.classList.remove( 'post-social-share__tooltip--visible' );
                } );

                // Copy to clipboard on click
                button.addEventListener( 'click', function () {
                    const url = this.getAttribute( 'data-url' );

                    // Hide hover tooltip
                    hoverTooltip.classList.remove( 'post-social-share__tooltip--visible' );

                    // Copy to clipboard
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText( url ).then( function () {
                            // Show "Copied!" tooltip
                            copiedTooltip.classList.add( 'post-social-share__tooltip--visible' );

                            // Hide "Copied!" tooltip after 2 seconds
                            setTimeout( function () {
                                copiedTooltip.classList.remove( 'post-social-share__tooltip--visible' );
                            }, 2000 );
                        } ).catch( function ( err ) {
                            console.error( 'Failed to copy: ', err );
                        } );
                    } else {
                        // Fallback for older browsers
                        const textArea = document.createElement( 'textarea' );
                        textArea.value = url;
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        document.body.appendChild( textArea );
                        textArea.focus();
                        textArea.select();

                        try {
                            document.execCommand( 'copy' );
                            // Show "Copied!" tooltip
                            copiedTooltip.classList.add( 'post-social-share__tooltip--visible' );

                            // Hide "Copied!" tooltip after 2 seconds
                            setTimeout( function () {
                                copiedTooltip.classList.remove( 'post-social-share__tooltip--visible' );
                            }, 2000 );
                        } catch (err) {
                            console.error( 'Failed to copy: ', err );
                        }

                        document.body.removeChild( textArea );
                    }
                } );
            } );
        } );
    })();
</script>
