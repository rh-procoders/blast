<?php
/**
 * Hero Home Block - Helper Functions
 * 
 * Block registration: handled by block.json
 * Field definitions: handled by fields.json (auto-loaded by ACF)
 * 
 * This file contains custom helper functions and logic for the Hero Home block.
 */

// Any custom functions or logic for this block can go here
// For example: custom validation, data processing, etc.

// Example: Custom function to process hero data
function hero_home_process_images($images) {
    if (!$images) return array();
    
    $processed = array();
    foreach ($images as $image) {
        $processed[] = array(
            'url' => $image['url'],
            'alt' => $image['alt'],
            'sizes' => $image['sizes'],
        );
    }
    return $processed;
}