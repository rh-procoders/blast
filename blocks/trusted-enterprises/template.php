<?php
/**
 * Trusted Enterprises Block
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'trusted-enterprises-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'te-trusted-enterprises';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

$heading = get_field('heading') ?: 'Trusted by Enterprises, Built for Scale.';
$features = get_field('features') ?: [];
$button = get_field('cta_url') ?: null;

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);

?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="te-container">
        <div class="te-content-wrapper">
            <!-- Left Side Heading -->
            <div class="te-heading-section">
                <h2 class="te-heading"><?php echo wp_kses_post($heading); ?></h2>
            </div>

            <!-- Right Side Features & CTA -->
            <div class="te-features-section">
                <div class="te-features-grid">
                    <?php if (!empty($features)) : ?>
                        <?php foreach ($features as $feature) : ?>
                            <div class="te-feature-item">
                                <h3 class="te-feature-title"><?php echo esc_html($feature['feature_title']); ?></h3>
                                <p class="te-feature-description"><?php echo wp_kses_post($feature['feature_description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                        <!-- CTA Button -->
                        <div class="te-cta-wrapper">
                            <a  href="<?php echo esc_url($button['url']); ?>"  class="btn has-coral-background-color te-cta-button">
                                <span class="button-text"
                                    data-hover-text="<?php echo esc_html($button['title'] ?: 'Get Demo'); ?>">
                                    <?php echo esc_html($button['title'] ?: 'Get Demo'); ?>
                                </span>
        
                                <span class="button-arrow-wrapper">
                                    <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>
                                </span>
                            </a>
        
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
