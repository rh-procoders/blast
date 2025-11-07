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

<article class="blog-filter-item">
    <!-- Thumbnail -->
    <a href="<?= esc_url( get_permalink() ) ?>" class="blog-filter-item__image-link">
        <?php if ( has_post_thumbnail() ): ?>
            <?php the_post_thumbnail( 'large', [ 'class' => 'blog-filter-item__image' ] ); ?>
        <?php else: ?>
            <div class="blog-filter-item__image-placeholder">
                <!-- Placeholder for posts without featured image -->
            </div>
        <?php endif; ?>
    </a>

    <!-- Content -->
    <div class="blog-filter-item__content">
        <!-- Meta: Categories + Read Time -->
        <div class="blog-filter-item__meta">
            <?php if ( ! empty( $categories ) ): ?>
                <div class="blog-filter-item__categories">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= esc_url( get_category_link( $category->term_id ) ) ?>"
                           class="blog-filter-item__category">
                            <?= esc_html( $category->name ) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <span class="blog-filter-item__read-time">
                <?php
                /* translators: %d: reading time in minutes */
                printf( esc_html__( '%d min read', 'blast-2025' ), $reading_time );
                ?>
            </span>
        </div>

        <!-- Title -->
        <h3 class="blog-filter-item__title">
            <a href="<?= esc_url( get_permalink() ) ?>">
                <?= esc_html( get_the_title() ) ?>
            </a>
        </h3>

        <!-- Excerpt -->
        <?php if ( has_excerpt() ): ?>
            <div class="blog-filter-item__excerpt">
                <?= wp_kses_post( get_the_excerpt() ) ?>
            </div>
        <?php endif; ?>

        <!-- Author -->
        <?php
        get_template_part( 'template-parts/components/author', null, [
                'user_id'     => get_the_author_meta( 'ID' ),
                'avatar_size' => 48
        ] );
        ?>
    </div>
</article>
