<?php
/**
 * Template part: Newsroom Item
 * Displays a single newsroom post card in the grid
 *
 * @package blast-2025
 */

// Get publication date
$pub_date = get_the_date( 'j M Y' );

// Get featured image or use fallback
$featured_image = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'medium' ) : '';

// Get excerpt or post content excerpt
$excerpt = wp_trim_words( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20, '...' );
$title = wp_trim_words( get_the_title(), 13, '...' );

$external_link = get_field('external_link', get_the_ID() );
if ( $external_link ) {
    $post_link = $external_link;
} else {
    $post_link = get_permalink();
}
?>

<a href="<?= esc_url( $post_link ) ?>" target="_blank" rel="noopener noreferrer" class="newsroom-item__link">
    <article class="newsroom-item card-block">
        <!-- Image Container -->
        <div class="newsroom-item__image-wrapper">
            <?php if ( $featured_image ): ?>
                <figure class="newsroom-item__image-figure">
                    <img src="<?= esc_url( $featured_image ) ?>" alt="<?= esc_attr( get_the_title() ) ?>" class="newsroom-item__image" loading="lazy">
                </figure>
            <?php else: ?>
                <div class="newsroom-item__image-placeholder">
                    <!-- Placeholder for posts without featured image -->
                </div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="newsroom-item__content">
            <!-- Publication Date -->
            <div class="newsroom-item__date">
                <span class="newsroom-item__date-text"><?= esc_html( $pub_date ) ?></span>
            </div>

            <!-- Title -->
            <h3 class="newsroom-item__title">
                <?= esc_html( $title ) ?>
            </h3>

            <!-- Excerpt -->
            <div class="newsroom-item__excerpt">
                <?= esc_html( $excerpt ) ?>
            </div>

            <!-- Source/Logo (if custom field exists) -->
            <?php 
            $source_logo = get_field( 'newsroom_source_logo' );
            if ( $source_logo ): 
            ?>
                <div class="newsroom-item__source">
                    <?php if ( is_array( $source_logo ) ): ?>
                        <img src="<?= esc_url( $source_logo['url'] ) ?>" alt="<?= esc_attr( $source_logo['alt'] ?? 'Source' ) ?>" class="newsroom-item__source-logo" loading="lazy">
                    <?php else: ?>
                        <img src="<?= esc_url( $source_logo ) ?>" alt="Source Logo" class="newsroom-item__source-logo" loading="lazy">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </article>
</a>
