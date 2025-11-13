<?php
/**
 * Leadership Block Helper Functions
 * 
 * Additional functionality for the leadership block
 */

// Ensure block scripts are properly enqueued
function leadership_block_enqueue_scripts() {
    if (is_admin()) {
        return; // Don't load on admin
    }
    
    $block_dir_uri = get_template_directory_uri() . '/blocks/leadership/';
    
    // Enqueue the JavaScript file
    wp_enqueue_script(
        'leadership-block-js',
        $block_dir_uri . 'js/script.js',
        array(),
        '1.0.0',
        true
    );
}

// Hook the script loading
add_action('wp_enqueue_scripts', function() {
    global $post;
    
    // Check if we're on a page/post that contains the leadership block
    if (has_blocks($post->post_content ?? '')) {
        $blocks = parse_blocks($post->post_content);
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'acf/leadership') {
                leadership_block_enqueue_scripts();
                break;
            }
        }
    }
});

/**
 * Sanitize member biography content
 */
function leadership_sanitize_bio($bio) {
    // Allow specific HTML tags for formatting
    $allowed_tags = array(
        'p' => array(),
        'br' => array(),
        'strong' => array(),
        'em' => array(),
        'b' => array(),
        'i' => array(),
        'u' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'ul' => array(),
        'ol' => array(),
        'li' => array(),
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
            'rel' => array()
        )
    );
    
    return wp_kses($bio, $allowed_tags);
}

/**
 * Get optimized image size for member photos
 */
function leadership_get_optimized_image($image_array, $size = 'medium') {
    if (!$image_array || !isset($image_array['sizes'])) {
        return null;
    }
    
    // Return the specified size if available
    if (isset($image_array['sizes'][$size])) {
        return array(
            'url' => $image_array['sizes'][$size],
            'width' => $image_array['sizes'][$size . '_width'] ?? 400,
            'height' => $image_array['sizes'][$size . '_height'] ?? 400,
            'alt' => $image_array['alt'] ?? ''
        );
    }
    
    // Fallback to original size
    return array(
        'url' => $image_array['url'],
        'width' => $image_array['width'] ?? 400,
        'height' => $image_array['height'] ?? 400,
        'alt' => $image_array['alt'] ?? ''
    );
}