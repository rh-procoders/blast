<?php
/**
 * About Backed By Block Template
 *
 * A gallery section showing company backers, investors, or partners with logos
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'about-backed-by-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'about-backed-by-block';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get block fields
$heading = get_field('backed_by_heading') ?: 'Backed by';
$logos = get_field('backed_by_logos');

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
    <div class="about-backed-by-block__container">
        <?php if ($heading): ?>
            <div class="about-backed-by-block__header">
                <h2 class="about-backed-by-block__heading"><?php echo esc_html($heading); ?></h2>
            </div>
        <?php endif; ?>

        <?php if ($logos): ?>
            <div class="about-backed-by-block__grid">
                <?php foreach ($logos as $logo): ?>
                    <?php 
                    $image = $logo['logo_image'];
                    $name = $logo['logo_name'];
                    $link = $logo['logo_link'];
                    ?>
                    
                    <div class="about-backed-by-item">
                        <?php if ($link): ?>
                            <a 
                                href="<?php echo esc_url($link); ?>" 
                                class="about-backed-by-item__link"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Visit <?php echo esc_attr($name); ?> website"
                            >
                        <?php endif; ?>
                        
                        <div class="about-backed-by-item__card">
                            <?php if ($image): ?>
                                <div class="about-backed-by-item__image-wrapper">
                                    <img 
                                        src="<?php echo esc_url($image['url']); ?>" 
                                        alt="<?php echo esc_attr($name); ?> logo"
                                        class="about-backed-by-item__image"
                                        width="<?php echo esc_attr($image['width']); ?>"
                                        height="<?php echo esc_attr($image['height']); ?>"
                                    >
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($link): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="about-backed-by-block__empty">
                <p>No backer logos added yet. Add some logos in the block settings.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>