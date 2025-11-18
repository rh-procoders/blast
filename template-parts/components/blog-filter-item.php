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
            <!-- Meta: Categories + Read Time -->
            <div class="blog-filter-item__meta">
                <?php if ( ! empty( $categories ) ): ?>
                    <div class="blog-filter-item__categories">
                        <?php foreach ($categories as $category): ?>
                            <span class="blog-filter-item__category">
                                <?= esc_html( $category->name ) ?>
                            </span>
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
                <?= esc_html( get_the_title() ) ?>
            </h3>

            <!-- Excerpt -->
            <div class="blog-filter-item__excerpt">
                <?= wp_kses_post( get_the_excerpt() ) ?>
            </div>

            <!-- Footer -->
            <div class="blog-filter-item__footer">
                <?php
                get_template_part( 'template-parts/components/author', null, [
                        'user_id'     => get_the_author_meta( 'ID' ),
                        'avatar_size' => 50
                ] );
                ?>
            </div>
        </div>
    </article>
</a>
