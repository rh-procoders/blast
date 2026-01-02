<?php
/**
 * Template part: Related Webinar Item
 * Displays a single webinar card in the related webinars section
 *
 * @package blast-2025
 */

?>

<a href="<?= esc_url( get_permalink() ) ?>" class="related-post-item__link">
    <article class="related-post-item card-block">
        <!-- Thumbnail -->
        <div class="related-post-item__image-wrapper">
            <?php if ( has_post_thumbnail() ): ?>
                <figure class="related-post-item__image-figure">
                    <?php the_post_thumbnail( 'large', [ 'class' => 'related-post-item__image' ] ); ?>
                </figure>
            <?php else: ?>
                <div class="related-post-item__image-placeholder">
                    <!-- Placeholder for posts without featured image -->
                </div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="related-post-item__content">
            <!-- Title -->
            <h3 class="related-post-item__title">
                <?= esc_html( get_the_title() ) ?>
            </h3>

            <!-- Footer: Author (inline name) -->
            <div class="related-post-item__footer">
				<span class="cta">
                    <span><?= __( 'Watch Now', 'blast-2025' ) ?></span>
                    <span><?php sprite_svg( 'webinar-cta-arrow-right', 24, 24 ) ?></span>
                </span>
            </div>
        </div>
    </article>
</a>
