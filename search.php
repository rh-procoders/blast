<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package blast-2025
 */

get_header();
?>

	<div class="blog-hero-wrap">
		<?php get_template_part( 'template-parts/global/blog-hero' ); ?>
	</div>

	<div class="container">
		<?php
		if ( have_posts() ) :

			?>
			<div class="posts-row">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'post_card' );

			endwhile;
			?>
			</div>
			<div class="pagination">
			<?php


			global $wp_query;

			$big = 999999999; // a big number for replacing

			echo paginate_links([
			'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
			'format' => '?paged=%#%',
			'current' => max(1, get_query_var('paged')),
			'total' => $wp_query->max_num_pages,
			'mid_size' => 2,
			'prev_text' => __('« Prev'),
			'next_text' => __('Next »'),
			]);
			?>

			</div>

		<?php else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
	</div>

<?php
get_footer();
