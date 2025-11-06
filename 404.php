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
		<div id="error-wrapper">
			<div class="inner-wrapper">
				<div class="content-wrapper section">
					<h1 data-v-6dcd59d9="">Page not found</h1>
					<p class="link-wrapper" >Return to <a href="<?php echo get_home_url(); ?>" class="link nuxt-link-active">Home page</a>
					</p>
				</div>
			</div>
		</div>


	</main><!-- #main -->

<?php
get_footer();
