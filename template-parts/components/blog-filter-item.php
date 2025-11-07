<?php
/**
 * Template part: Blog Filter Item
 * Displays a single post card in the blog filter grid
 *
 * @package blast-2025
 */
?>

<article class="blog-filter-item">

    <a href="<?= esc_url(get_permalink()) ?>" class="blog-filter-item__image">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('large', ['class' => 'blog-filter-item__thumbnail']); ?>
        <?php else: ?>
            <div class="blog-filter-item__placeholder">
                <!-- Placeholder for posts without featured image -->
            </div>
        <?php endif; ?>
    </a>

    <div class="blog-filter-item__content">

        <time class="blog-filter-item__date" datetime="<?= esc_attr(get_the_date('c')) ?>">
            <?= esc_html(get_the_date('M j, Y')) ?>
        </time>

        <h3 class="blog-filter-item__title">
            <a href="<?= esc_url(get_permalink()) ?>">
                <?= esc_html(get_the_title()) ?>
            </a>
        </h3>

        <div class="blog-filter-item__excerpt">
            <?= wp_kses_post(get_the_excerpt()) ?>
        </div>

        <a href="<?= esc_url(get_permalink()) ?>" class="blog-filter-item__read-more">
            <span><?= esc_html__('Read More', 'blast-2025') ?></span>
            <!-- <?php /* sprite_svg('icon-arrow', 18, 18) */ ?> -->
        </a>

    </div>

</article>