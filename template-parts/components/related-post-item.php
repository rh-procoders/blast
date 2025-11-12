<?php
/**
 * Template part: Related Post Item
 * Displays a single post card in the related posts section
 * Similar to blog-filter-item but with inline author name
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
$reading_time = bs_get_reading_time();
?>

<article class="related-post-item card-block">
	<!-- Thumbnail -->
	<a href="<?= esc_url( get_permalink() ) ?>" class="related-post-item__image-link">
		<?php if ( has_post_thumbnail() ): ?>
			<?php the_post_thumbnail( 'large', [ 'class' => 'related-post-item__image' ] ); ?>
		<?php else: ?>
			<div class="related-post-item__image-placeholder">
				<!-- Placeholder for posts without featured image -->
			</div>
		<?php endif; ?>
	</a>

	<!-- Content -->
	<div class="related-post-item__content">
		<!-- Meta: Categories + Read Time -->
		<div class="related-post-item__meta">
			<?php if ( ! empty( $categories ) ): ?>
				<div class="related-post-item__categories">
					<?php foreach ($categories as $category): ?>
						<span class="related-post-item__category">
                            <?= esc_html( $category->name ) ?>
                        </span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<span class="related-post-item__read-time">
                <?php
                /* translators: %d: reading time in minutes */
                printf( esc_html__( '%d min read', 'blast-2025' ), $reading_time );
                ?>
            </span>
		</div>

		<!-- Title -->
		<h3 class="related-post-item__title">
			<a href="<?= esc_url( get_permalink() ) ?>">
				<?= esc_html( get_the_title() ) ?>
			</a>
		</h3>

		<!-- Excerpt -->
		<?php if ( has_excerpt() ): ?>
			<div class="related-post-item__excerpt">
				<?= wp_kses_post( get_the_excerpt() ) ?>
			</div>
		<?php endif; ?>

		<!-- Footer: Author (inline name) -->
		<div class="related-post-item__footer">
			<?php
			get_template_part( 'template-parts/components/author', null, [
				'user_id'     => get_the_author_meta( 'ID' ),
				'avatar_size' => 50,
				'inline'      => true // ← Key difference: inline author name
			] );
			?>
		</div>
	</div>
</article>
