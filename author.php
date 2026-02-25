<?php
/**
 * The template for displaying author archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

get_header();
?>

	<div class="blog-hero-wrap">
		<?php 
		// Custom hero for author pages
		$author = get_queried_object();
		?>
		<div class="container">
			<div class="breadcrumbs">
				<ul class="breadcrumbs-list">
					<li>
						<a href="<?php echo home_url('/'); ?>">Home</a>
					</li>
					<li><span class="separator">|</span></li>
					<li>
						<a href="<?php echo get_post_type_archive_link( 'post' ); ?>">Blog</a>
					</li>
					<li><span class="separator">|</span></li>
					<li>
						<span class="title">By <?php echo esc_html( $author->display_name ); ?></span>
					</li>
				</ul>
			</div>
			<div class="hero-page__content">
				<h1 class="hero-page__title">
					<?php printf( esc_html__( 'Posts by %s', 'blast-2025' ), esc_html( $author->display_name ) ); ?>
				</h1>
			</div>
		</div>
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
				 * Using post_card to show excerpts, not full content (SEO best practice)
				 */
				get_template_part( 'template-parts/content', 'post_card' );

			endwhile;
			?>
			</div>
			<div class="pagination">
			<?php
				global $wp_query;
				$big = 999999999;

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
