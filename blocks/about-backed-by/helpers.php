<?php
/**
 * About Backed By Block Helper Functions
 * 
 * Additional functionality for the about-backed-by block
 */

/**
 * Get optimized image for logos
 */
function backed_by_get_optimized_image($image_array, $size = 'medium') {
    if (!$image_array || !is_array($image_array)) {
        return null;
    }
    
    // For logos, we typically want the full size for best quality
    return array(
        'url' => $image_array['url'] ?? '',
        'width' => $image_array['width'] ?? 200,
        'height' => $image_array['height'] ?? 100,
        'alt' => $image_array['alt'] ?? ''
    );
}

/**
 * Validate and sanitize logo data
 */
function backed_by_sanitize_logo($logo) {
    if (!is_array($logo)) {
        return null;
    }
    
    return array(
        'logo_image' => $logo['logo_image'] ?? null,
        'logo_name' => sanitize_text_field($logo['logo_name'] ?? ''),
        'logo_link' => esc_url_raw($logo['logo_link'] ?? '')
    );
}

/**
 * Check if image is SVG for special handling
 */
function backed_by_is_svg($image_url) {
    return str_ends_with(strtolower($image_url), '.svg');
}

/**
 * Get image dimensions for responsive sizing
 */
function backed_by_get_responsive_image_attrs($image, $max_width = 200, $max_height = 60) {
    if (!$image) {
        return '';
    }
    
    $width = $image['width'] ?? $max_width;
    $height = $image['height'] ?? $max_height;
    
    // Calculate aspect ratio and constrain to max dimensions
    $ratio = min($max_width / $width, $max_height / $height);
    
    if ($ratio < 1) {
        $width = round($width * $ratio);
        $height = round($height * $ratio);
    }
    
    return sprintf(
        'width="%d" height="%d" style="max-width: %dpx; max-height: %dpx;"',
        esc_attr($width),
        esc_attr($height),
        esc_attr($max_width),
        esc_attr($max_height)
    );
}