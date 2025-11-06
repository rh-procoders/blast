<?php
/**
 * Hero Home Block Template
 *
 * Creates a hero section with Gutenberg blocks on the left and YouTube video on the right
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'animation-section-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'animation-section';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$lottie_animation_desktop = get_field('lottie_animation');
$lottie_animation_mobile = get_field('lottie_animation_mobile');

// Fallback: if only one is set, use it for both
if (!$lottie_animation_desktop && $lottie_animation_mobile) {
    $lottie_animation_desktop = $lottie_animation_mobile;
}
if (!$lottie_animation_mobile && $lottie_animation_desktop) {
    $lottie_animation_mobile = $lottie_animation_desktop;
}

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);

// Define allowed inner blocks template for the left content area
$allowed_blocks = [
    [ 'core/paragraph', [ 
        'placeholder' => 'The preemptive gap',
        'className' => 'pre-heading-section'
    ] ],
    [ 'core/heading', [ 
        'level' => 2,
        'placeholder' => 'Preemptive Security That Never Slows You Down',
        'className' => 'heading-section'
    ] ],
    [ 'core/paragraph', [ 
        'placeholder' => 'Chasing alerts, fixing misconfigurations, patching drift, and reacting to AI-driven threats, your team can’t keep up — and as your environment grows, your defense gap widens.',
        'className' => 'paragraph-section'
    ] ],
    
];

// Render InnerBlocks properly
$inner_blocks = '<InnerBlocks template="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />';
?>

<?php if (isset( $block['data']['preview_image_help'] )  ): ?>
	<?php 
	$fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__), );
	echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	?>
<?php else: ?>

<section <?php echo $wrapper_attributes; ?>>
    <!-- Background Lottie Animation -->
    <div class="animation-section__lottie">
        <?php if ($lottie_animation_desktop || $lottie_animation_mobile): ?>
            <!-- Desktop Lottie Animation -->
            <?php if ($lottie_animation_desktop): ?>
                <lottie-player 
                    id="lottie-desktop-<?php echo $block['id']; ?>"
                    class="lottie-desktop"
                    src="<?php echo esc_url( $lottie_animation_desktop ); ?>"  
                    background="transparent"  
                    speed="0.6"  
                    style="width: 100%; height: 100%;"  
                    data-animation-on-scroll>
                </lottie-player>
            <?php endif; ?>
            
            <!-- Mobile Lottie Animation -->
            <?php if ($lottie_animation_mobile): ?>
                <lottie-player 
                    id="lottie-mobile-<?php echo $block['id']; ?>"
                    class="lottie-mobile"
                    src="<?php echo esc_url( $lottie_animation_mobile ); ?>"  
                    background="transparent"  
                    speed="0.6"  
                    style="width: 100%; height: 100%;"  
                    data-animation-on-scroll>
                </lottie-player>
            <?php endif; ?>
        <?php else: ?>
            <p style="text-align: center; padding: 50px; background: #f0f0f0; border: 2px dashed #ccc;">
                No Lottie animation files selected. Please add Lottie JSON files for desktop and/or mobile in the block settings.
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Container -->
    <div class="animation-section__container container container--xl">
        
        <!-- Left Content Area: InnerBlocks -->
        <div class="animation-section__content">
            <?php echo $inner_blocks; ?>
        </div>
                
    </div>
</section>

<?php endif; ?>