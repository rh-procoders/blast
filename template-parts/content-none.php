<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

?>

<section class="no-results not-found">
	<header class="page-header search-header">
		<h1 class="page-title">
			<?php
			if ( is_search() ){
			/* translators: %s: search query. */
				printf( esc_html__( 'Search Results for %s', 'blast-2025' ), '<span>' . get_search_query() . '</span>' );
			}else{
				esc_html_e( 'Nothing Found', 'blast-2025' );
			}
			?>	
		</h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'blast-2025' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, no results were found.', 'blast-2025' ); ?></p>
			<?php
			get_template_part( 'template-parts/global/search-bar' ); 

		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'blast-2025' ); ?></p>
			<?php
			get_template_part( 'template-parts/global/search-bar' ); 

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
