<?php
/**
 * Hero Product Block Template
 *
 * Creates a hero section with Gutenberg blocks on the left and YouTube video on the right
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'product-hero-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'product-hero';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$youtube_video = get_field('youtube_video');
$video_autoplay = get_field('video_autoplay');
$video_loop = get_field('video_loop');
$animation_enabled = get_field('animation_enabled');

if($animation_enabled){
    $classes .= ' product-hero--animate';
}

// Convert YouTube URL to embed format
$youtube_embed_url = '';
if ($youtube_video) {
    $video_id = '';
    if (preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube_video, $matches)) {
        $video_id = $matches[1];
    } elseif (preg_match('/youtu\\.be\\/([^\\?\\&]+)/', $youtube_video, $matches)) {
        $video_id = $matches[1];
    }
    
    if ($video_id) {
        $autoplay_param = $video_autoplay ? '&autoplay=1&mute=1' : '';
        $loop_param = $video_loop ? '&loop=1&playlist=' . $video_id : '';
        $youtube_embed_url = "https://www.youtube.com/embed/{$video_id}?controls=0&showinfo=0&rel=0&modestbranding=1{$autoplay_param}{$loop_param}";
    }
}

$hero_image = get_field('hero_image');

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);

// Define allowed inner blocks template for the left content area
$allowed_blocks = [
    [ 'core/heading', [ 
        'level' => 1,
        'placeholder' => 'Preemptive Security That Never Slows You Down',
        'className' => 'hero-heading'
    ] ],
    [ 'core/paragraph', [ 
        'placeholder' => 'Blast keeps your clouds on track, elevating your native controls into a resilient, preventive defense that moves at full speed.',
    ] ],
    [ 'core/buttons', [] ],
    [ 'core/spacer', [] ]
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
    <!-- Container -->
    <div class="product-hero__container container-full">
        
        <!-- Left Content Area: InnerBlocks -->
        <div class="product-hero__content">
            <?php echo $inner_blocks; ?>
        </div>
        
        <!-- Right Video Area -->
        <div class="product-hero__media">
            <div class="product-hero__media-container">
                <?php if ($youtube_embed_url): ?>
                    <div class="product-hero__video-wrapper">
                        <iframe 
                            class="product-hero__video-iframe"
                            src="<?php echo esc_url($youtube_embed_url); ?>"
                            title="Hero Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif($hero_image): ?>
                    <div class="product-hero__image-wrapper">
                        <img 
                            class="product-hero__hero-image"
                            src="<?php echo esc_url($hero_image['sizes']['large'] ?: $hero_image['url']); ?>" 
                            alt="<?php echo esc_attr($hero_image['alt'] ?: 'Product Hero Image'); ?>"
                            loading="lazy">
                    </div>
                <?php else: ?>
                    <!-- Placeholder when no video is set -->
                    <div class="product-hero__video-placeholder">
                        <div class="product-hero__placeholder-content">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 5v14l11-7L8 5z" fill="currentColor"/>
                            </svg>
                            <p>Video Placeholder</p>
                            <small>Add a YouTube URL in the block settings</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<?php endif; ?>