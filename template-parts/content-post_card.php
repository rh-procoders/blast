<?php
/**
 * Template part for displaying post cards in archives (blog list, author pages, etc)
 * Shows excerpt instead of full content to prevent duplicate content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

// Get post categories (exclude Uncategorized)
$categories = get_the_category();
if ( $categories ) {
	$categories = array_filter( $categories, function ( $cat ) {
		return $cat->slug !== 'uncategorized';
	} );
}

// Get reading time
$reading_time = function_exists( 'bs_get_reading_time' ) ? bs_get_reading_time() : '';
?>

<a href="<?= esc_url( get_permalink() ) ?>" class="blog-filter-item__link">
	<article class="blog-filter-item card-block">
		<!-- Thumbnail -->
		<?php if ( has_post_thumbnail() ): ?>
			<div class="blog-filter-item__image-wrapper">
				<figure class="blog-filter-item__image-figure">
					<?php the_post_thumbnail( 'large', [ 'class' => 'blog-filter-item__image' ] ); ?>
				</figure>
			</div>
		<?php endif; ?>

		<!-- Category Tags -->
		<?php if ( ! empty( $categories ) ): ?>
			<div class="blog-filter-item__tags">
				<?php foreach ( $categories as $cat ): ?>
					<span class="blog-filter-item__tag">
						<?php echo esc_html( $cat->name ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<!-- Title -->
		<h3 class="blog-filter-item__title">
			<a href="<?= esc_url( get_permalink() ) ?>">
				<?php the_title(); ?>
			</a>
		</h3>

		<!-- Excerpt (not full content - prevents duplicate content issues) -->
		<div class="blog-filter-item__excerpt">
			<?= wp_kses_post( get_the_excerpt() ) ?>
		</div>

		<!-- Footer: Author & Reading Time -->
		<div class="blog-filter-item__footer">
			<?php
			get_template_part( 'template-parts/components/author', null, [
				'user_id'     => get_the_author_meta( 'ID' ),
				'avatar_size' => 50
			] );
			?>
		</div>
	</article>
</a>
