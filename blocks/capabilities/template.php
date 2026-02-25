<?php
/**
 * Capabilities Block
 */

$tag = get_field('tag') ?: 'Defense Planning';
$heading = get_field('heading') ?: 'Adaptive Guardrail Planning Engine';
$description = get_field('description') ?: '';
$features_label = get_field('features_label') ?: 'Features';
$features = get_field('features') ?: [];
$image = get_field('image') ?: null;

$classes = 'cap-capabilities-section';

if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

$id = 'capabilities-' . $block['id'];

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);

?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="cap-container">
        <div class="cap-content-wrapper">
            <!-- Left Side Content -->
            <div class="cap-left-content">
                <!-- Tag -->
                <?php if (!empty($tag)) : ?>
                    <span class="cap-tag"><?php echo esc_html($tag); ?></span>
                <?php endif; ?>

                <!-- Heading -->
                <?php if (!empty($heading)) : ?>
                    <h2 class="cap-heading"><?php echo wp_kses_post($heading); ?></h2>
                <?php endif; ?>

                <!-- Description -->
                <?php if (!empty($description)) : ?>
                    <div class="cap-description"><?php echo wp_kses_post($description); ?></div>
                <?php endif; ?>

                <!-- Features Section -->
                <?php if (!empty($features)) : ?>
                    <div class="cap-features">
                        <?php if (!empty($features_label)) : ?>
                            <div class="cap-features-label"><?php echo esc_html($features_label); ?></div>
                        <?php endif; ?>
                        <div class="cap-features-grid">
                            <?php foreach ($features as $index => $feature) : ?>
                                <div class="cap-feature-item">
                                    <p><?php echo wp_kses_post($feature['feature_text']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side Image -->
            <div class="cap-right-image">
                <?php if (!empty($image)) : ?>
                    <img 
                        src="<?php echo esc_url($image['url']); ?>" 
                        alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
                        class="cap-image"
                    />
                <?php else : ?>
                    <div class="cap-image-placeholder">
                        <p>Image will appear here</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
