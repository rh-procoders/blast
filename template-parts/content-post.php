<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="container container--lg">

		<div class="entry-content">
		<?php
		the_title( '<h1 class="post-title">', '</h1>' );
		?>
		<?php 
		the_post_thumbnail('post-thumbnail', ['class' => 'post-featured-image', 'title' => get_the_title() ]);
		?>
			
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

		</div><!-- .entry-content -->
	</div>


</article><!-- #post-<?php the_ID(); ?> -->
