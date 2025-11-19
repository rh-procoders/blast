<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package blast-2025
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="inner-wrapper">
			<img src="<?php echo THEME_URI ?>/img/Larry_v3_right.png" alt="Larry_v3_right" class="right-image">
			<img src="<?php echo THEME_URI ?>/img/Larry_v3_left.png" alt="Larry_v3_left" class="left-image">
			<div class="content-wrapper section">
				<h1>404: Unauthorized Page Request</h1>
				<p>Everything’s secure, this page simply doesn’t exist. The homepage is ready when you are.</p>
				<a href="<?php echo get_home_url(); ?>"  class="btn has-dark-blue-background-color">
					<span class="button-text"
						data-hover-text="Back to Homepage">
						Back to Homepage
					</span>
					<span class="button-arrow-wrapper">
						<?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>
					</span>
				</a>
			</div>
		</div>
	</main><!-- #main -->
<?php
get_footer();
