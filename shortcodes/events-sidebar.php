<?php
declare(strict_types=1);

/**
 * Events Sidebar Shortcode
 * Displays sidebar with widgets and social share buttons
 *
 * @package blast_Wp
 */

/**
 * Shortcode: [events-sidebar]
 *
 * @return string HTML output
 */
function blast_events_sidebar_shortcode(): string
{
	ob_start();
	?>

	<!-- Widget & Socials -->
	<div class="events-sidebar">
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

	<?php
	return ob_get_clean();
}

add_shortcode( 'events-sidebar', 'blast_events_sidebar_shortcode' );
