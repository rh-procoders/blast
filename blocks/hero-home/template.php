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
$id = 'hero-home-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'hero-home';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$local_video_files = get_field('local_video_files');
$youtube_video = get_field('youtube_video');
$vimeo_video = get_field('vimeo_video');
$video_autoplay = get_field('video_autoplay');
$video_loop = get_field('video_loop');
$animation_enabled = get_field('animation_enabled');
$video_poster = get_field('video_poster');

if($animation_enabled){
    $classes .= ' hero-home--animate';
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

// Convert Vimeo URL to embed format
$vimeo_embed_url = '';
if ($vimeo_video) {
    $video_id = '';
    if (preg_match('/vimeo\\.com\\/([0-9]+)/', $vimeo_video, $matches)) {
        $video_id = $matches[1];
    }
    
    if ($video_id) {
        $autoplay_param = $video_autoplay ? '&autoplay=1&muted=1' : '';
        $loop_param = $video_loop ? '&loop=1' : '';
        $vimeo_embed_url = "https://player.vimeo.com/video/{$video_id}?{$autoplay_param}{$loop_param}";
    }
}

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
    <!-- Background Gradient Overlay -->
    <div class="hero-home__gradient-overlay">
        <img src="<?php echo THEME_URI ?>/img/hero-home-bg-desktop.png" alt="hero-home-bg-desktop" class="desktop-only">
        <img src="<?php echo THEME_URI ?>/img/home-hero-mobile-bg.png" alt="home-hero-mobile" class="mobile-only">
        
    </div>
    
    <!-- Container -->
    <div class="hero-home__container container container--xl">
        
        <!-- Left Content Area: InnerBlocks -->
        <div class="hero-home__content">
            <?php echo $inner_blocks; ?>
        </div>
        
        <!-- Right Video Area -->
        <div class="hero-home__media">
            <div class="hero-home__video-container">
                <?php if (!empty($local_video_files) && is_array($local_video_files)): ?>
                    <!-- HTML5 Local Video Player -->
                    <div class="hero-home__video-wrapper">
                        <video 
                            class="hero-home__html5-video"
                            controls
                            playsinline
                            width="100%"
                            height="auto"
                            <?php if ($video_autoplay): ?>autoplay muted<?php endif; ?>
                            <?php if ($video_loop): ?>loop<?php endif; ?>
                            <?php if ($video_poster): ?>poster="<?php echo esc_url($video_poster); ?>"<?php endif; ?>>
                            <?php foreach ($local_video_files as $video_item):
                                $video_url = $video_item['video_file']['url'] ?? '';
                                $mime_type = $video_item['video_file']['mime_type'] ?? '';
                                ?>
                                <?php if (!empty($video_url) && !empty($mime_type)): ?>
                                    <source src="<?php echo esc_url($video_url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php elseif ($youtube_embed_url): ?>
                    <!-- YouTube Embed -->
                    <div class="hero-home__video-wrapper">
                        <iframe 
                            class="hero-home__video-iframe"
                            src="<?php echo esc_url($youtube_embed_url); ?>"
                            title="Hero Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif ($vimeo_embed_url): ?>
                    <!-- Vimeo Embed -->
                    <div class="hero-home__video-wrapper">
                        <iframe 
                            class="hero-home__video-iframe"
                            src="<?php echo esc_url($vimeo_embed_url); ?>"
                            title="Hero Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php else: ?>
                    <!-- Placeholder when no video is set -->
                    <div class="hero-home__video-placeholder">
                        <div class="hero-home__placeholder-content">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 5v14l11-7L8 5z" fill="currentColor"/>
                            </svg>
                            <p>Video Placeholder</p>
                            <small>Add a local video, YouTube URL, or Vimeo URL in the block settings</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<?php endif; ?>