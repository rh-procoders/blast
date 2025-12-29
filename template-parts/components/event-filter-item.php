<?php
/**
 * Template part: Blog Filter Item
 * Displays a single post card in the blog filter grid
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

<a href="<?= esc_url( get_permalink() ) ?>" class="blog-filter-item__link">
    <article class="blog-filter-item card-block">
        <!-- Thumbnail -->
        <div class="blog-filter-item__image-wrapper">
            <?php if ( has_post_thumbnail() ): ?>
                <figure class="blog-filter-item__image-figure">
                    <?php the_post_thumbnail( 'large', [ 'class' => 'blog-filter-item__image' ] ); ?>
                </figure>
            <?php else: ?>
                <div class="blog-filter-item__image-placeholder">
                    <!-- Placeholder for posts without featured image -->
                </div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="blog-filter-item__content">
            <!-- Title -->
            <h3 class="blog-filter-item__title">
                <?= esc_html( get_the_title() ) ?>
            </h3>

            <!-- Excerpt -->
            <div class="blog-filter-item__excerpt">
                <?= wp_kses_post( get_the_excerpt() ) ?>
            </div>

            <!-- Footer -->
            <div class="blog-filter-item__footer">


            </div>
        </div>
    </article>
</a>
