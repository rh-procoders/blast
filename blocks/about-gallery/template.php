<?php
/**
 * About Gallery Block Template
 *
 * A creative gallery layout with scattered images, value tags, and call-to-action
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'about-gallery-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'about-gallery-block';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get block fields
$heading = get_field('gallery_heading') ?: 'Join our Team';
$button = get_field('gallery_button');
$gallery_items = get_field('gallery_items');

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);
?>

<?php if (isset( $block['data']['preview_image_help'] )  ): ?>
	<?php 
	$fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__), );
	echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	?>
<?php else: ?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="about-gallery-block__container">
        <!-- Central Content -->
        <div class="about-gallery-block__center">
            <?php if ($heading): ?>
                <h2 class="about-gallery-block__heading"><?php echo esc_html($heading); ?></h2>
            <?php endif; ?>
            
            <?php if ($button): ?>
                <a 
                    href="<?php echo esc_url($button['url']); ?>" 
                    class="about-gallery-block__button"
                    <?php echo $button['target'] ? 'target="' . esc_attr($button['target']) . '"' : ''; ?>
                    <?php echo $button['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                >
                    <?php echo esc_html($button['title'] ?: 'Open Jobs'); ?>
                    <svg class="about-gallery-block__button-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M3.33334 10H16.6667M16.6667 10L10 3.33334M16.6667 10L10 16.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <!-- Gallery Items -->
        <?php if ($gallery_items): ?>
            <div class="about-gallery-block__gallery">
                <?php foreach ($gallery_items as $index => $item): ?>
                    <?php 
                    $image = $item['item_image'];
                    $tag_text = $item['item_tag_text'];
                    $tag_color = $item['item_tag_color'] ?: 'purple';
                    ?>



                    <div class="about-gallery-item about-gallery-item--<?php echo $index + 1; ?>">
                        <?php if ($image): ?>
                            <div class="about-gallery-item__image-wrapper">
                                <img 
                                    src="<?php echo esc_url($image['sizes']['medium'] ?? $image['url']); ?>" 
                                    alt="<?php echo esc_attr($image['alt'] ?: 'Gallery image'); ?>"
                                    class="about-gallery-item__image"
                                    width="<?php echo esc_attr($image['sizes']['medium_width'] ?? $image['width']); ?>"
                                    height="<?php echo esc_attr($image['sizes']['medium_height'] ?? $image['height']); ?>"
                                >
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($tag_text): ?>
                            <div class="about-gallery-item__tag about-gallery-item__tag--<?php echo esc_attr($tag_color); ?>">
                                <span class="about-gallery-item__tag-text"><?php echo esc_html($tag_text); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="about-gallery-block__empty">
                <p>No gallery items added yet. Add some images and tags in the block settings.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>