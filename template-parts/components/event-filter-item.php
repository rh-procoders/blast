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

$event_banner_listing = get_field( 'epo__banner-listing' ) ?? null;
$event_start_date     = get_field( 'epo__start-date' ) ?? null;
$event_end_date       = get_field( 'epo__end-date' ) ?? null;
$event_location       = get_field( 'epo__location' ) ?? null;

// Priority: Custom ACF banner listing image > Featured image
if ( $event_banner_listing && ! empty( $event_banner_listing['ID'] ) ) {
	$post_thumbnail = wp_get_attachment_image( $event_banner_listing['ID'], 'large', false, [
		'class' => 'blog-filter-item__image',
	] );
} else {
	$post_thumbnail = get_the_post_thumbnail( get_the_ID(), 'large', [
		'class' => 'blog-filter-item__image',
	] );
}
?>

<a href="<?= esc_url( get_permalink() ) ?>" class="blog-filter-item__link">
    <article class="blog-filter-item card-block">
        <!-- Thumbnail -->
        <div class="blog-filter-item__image-wrapper">
            <?php if ( $post_thumbnail ): ?>
                <figure class="blog-filter-item__image-figure">
                    <?= $post_thumbnail ?>
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
                <?php
                if ( $event_start_date || $event_end_date || $event_location ) : ?>
                    <?= blast_format_event_meta(
                            $event_start_date,
                            $event_end_date,
                            $event_location
                    ); ?>
                <?php
                endif; ?>
            </div>
        </div>
    </article>
</a>
