<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (get_field('inner_container')): ?>
	<div class="inner-wrapper">
		<div class="content-wrapper section small">
	<?php endif; ?>
			<?php
			 if (get_field('hide_title') == false): ?> 
			<h2 class="title"><?php the_title(); ?></h2>
			<?php endif; ?>
			
			<?php
			the_content();
			?>
		<?php if (get_field('inner_container')): ?>
			</div>
		</div>
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
