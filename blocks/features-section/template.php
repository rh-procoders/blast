<?php
/**
 * Features Section Block Template
 *
 * A features section with gradient background, heading, and feature cards with icons
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'features-section-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'features-section';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$section_heading = get_field('section_heading') ?: "Lead with Prevention.\nWin with Confidence.";
$features = get_field('features') ?: [];
$layout_settings = get_field('layout_settings') ?: [];

// Layout settings with defaults
$columns_desktop = isset($layout_settings['columns_desktop']) ? $layout_settings['columns_desktop'] : '3';

// Add gradient class to main classes
$classes .= ' features-section--';
$classes .= ' features-section--cols-' . $columns_desktop;

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

<section <?php echo $wrapper_attributes; ?>>

    <img src="<?php echo THEME_URI ?>/img/featured-top-bg.svg" alt="top background graphic" class="features-section__bg-image-top">
    
    <div class="features-section__container container container--xl">
        
        <!-- Section Heading -->
        <div class="features-section__header">
            <h2 class="h1 features-section__heading">
                <?php echo nl2br(esc_html($section_heading)); ?>
            </h2>
        </div>
        
        <!-- Features Grid -->
        <?php if (!empty($features)): ?>
            <div class="features-section__grid">
                
                <?php foreach ($features as $feature): ?>
                    <div class="features-section__card">
                        
                        <!-- Feature Icon -->
                        <div class="features-section__icon">
                            <?php if (!empty($feature['icon'])): ?>
                                <img src="<?php echo esc_url($feature['icon']['sizes']['thumbnail'] ?: $feature['icon']['url']); ?>" 
                                     alt="<?php echo esc_attr($feature['title']); ?> icon"
                                     loading="lazy">
                            <?php endif; ?>
                            <!-- Feature Title -->
                            <h3 class="features-section__title">
                                <?php echo $feature['title']; ?>
                            </h3>
                        </div>
                        
                        <!-- Feature Content -->
                        <div class="features-section__content">
                            <!-- Feature Description -->
                            <p class="features-section__description">
                                <?php echo esc_html($feature['description']); ?>
                            </p>
                            
                        </div>
                        
                    </div>
                <?php endforeach; ?>
                
            </div>
        <?php else: ?>
            <div class="features-section__placeholder">
                <p>No features added yet. Please add features in the block settings.</p>
            </div>
        <?php endif; ?>
        
    </div>

    <img src="<?php echo THEME_URI ?>/img/featured-bottom-bg.svg" alt="bottom background graphic" class="features-section__bg-image-bottom">
    
</section>

<?php endif; ?>