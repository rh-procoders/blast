<?php
/**
 * About Gallery Block Helper Functions
 * 
 * Additional functionality for the about-gallery block
 */

/**
 * Get tag color classes and styles
 */
function about_gallery_get_tag_colors() {
    return array(
        'purple' => array(
            'class' => 'about-gallery-item__tag--purple',
            'gradient' => 'linear-gradient(135deg, #8B5FBF, #9D4EDD)',
            'color' => 'white'
        ),
        'blue' => array(
            'class' => 'about-gallery-item__tag--blue',
            'gradient' => 'linear-gradient(135deg, #4F8BF9, #2563EB)',
            'color' => 'white'
        ),
        'green' => array(
            'class' => 'about-gallery-item__tag--green',
            'gradient' => 'linear-gradient(135deg, #10B981, #059669)',
            'color' => 'white'
        ),
        'orange' => array(
            'class' => 'about-gallery-item__tag--orange',
            'gradient' => 'linear-gradient(135deg, #F59E0B, #D97706)',
            'color' => 'white'
        ),
        'red' => array(
            'class' => 'about-gallery-item__tag--red',
            'gradient' => 'linear-gradient(135deg, #EF4444, #DC2626)',
            'color' => 'white'
        ),
        'yellow' => array(
            'class' => 'about-gallery-item__tag--yellow',
            'gradient' => 'linear-gradient(135deg, #FCD34D, #F59E0B)',
            'color' => '#92400E'
        ),
        'pink' => array(
            'class' => 'about-gallery-item__tag--pink',
            'gradient' => 'linear-gradient(135deg, #EC4899, #DB2777)',
            'color' => 'white'
        )
    );
}

/**
 * Get optimized image for gallery items
 */
function about_gallery_get_optimized_image($image_array, $size = 'medium') {
    if (!$image_array || !isset($image_array['sizes'])) {
        return null;
    }
    
    // Return the specified size if available
    if (isset($image_array['sizes'][$size])) {
        return array(
            'url' => $image_array['sizes'][$size],
            'width' => $image_array['sizes'][$size . '_width'] ?? 300,
            'height' => $image_array['sizes'][$size . '_height'] ?? 200,
            'alt' => $image_array['alt'] ?? ''
        );
    }
    
    // Fallback to original size
    return array(
        'url' => $image_array['url'],
        'width' => $image_array['width'] ?? 300,
        'height' => $image_array['height'] ?? 200,
        'alt' => $image_array['alt'] ?? ''
    );
}

/**
 * Sanitize tag text
 */
function about_gallery_sanitize_tag_text($text) {
    return wp_kses($text, array());
}

/**
 * Get position class for gallery items
 */
function about_gallery_get_position_class($position) {
    $allowed_positions = array(
        'top-left',
        'top-right',
        'center-left',
        'center-right',
        'bottom-left',
        'bottom-right'
    );
    
    if (in_array($position, $allowed_positions)) {
        return 'about-gallery-item--' . $position;
    }
    
    return 'about-gallery-item--top-left'; // Default fallback
}