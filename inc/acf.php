<?php 

/**
 * UNIVERSAL ACF BLOCK FIELD SYNC SYSTEM
 * 
 * This system automatically handles ACF field groups for ANY block in the /blocks/ directory.
 * 
 * HOW IT WORKS:
 * 1. Create a block directory: /blocks/my-new-block/
 * 2. Register block with any namespace: blast/my-new-block, acf/my-new-block, etc.
 * 3. Create ACF fields in WordPress admin with block location rule
 * 4. Fields automatically save to: /blocks/my-new-block/fields.json
 * 
 * SUPPORTED BLOCK NAMESPACES:
 * - blast/block-name
 * - acf/block-name  
 * - any-namespace/block-name
 * - block-name (no namespace)
 * 
 * EXCLUDED BLOCKS:
 * - sample (add more to the excluded list in blast_get_available_blocks())
 */

// Custom ACF JSON save/load paths for block-level field groups
add_filter('acf/settings/save_json', 'blast_acf_json_save_point');
function blast_acf_json_save_point($path) {
    // Default to acf-json folder - we'll handle block-specific saving in the post_save hook
    return get_template_directory() . '/acf-json';
}

// Custom hook to handle field group save and move to correct location
add_action('acf/update_field_group', 'blast_acf_save_field_group_to_block', 1);
function blast_acf_save_field_group_to_block($field_group) {
    if (!$field_group || !isset($field_group['location'])) {
        return;
    }
    
    // Get all available blocks from the blocks directory
    $available_blocks = blast_get_available_blocks();
    
    // Check if this is a block field group
    foreach ($field_group['location'] as $location_group) {
        foreach ($location_group as $rule) {
            if ($rule['param'] === 'block' && $rule['operator'] === '==') {
                $block_value = $rule['value'];
                $block_name = blast_extract_block_name($block_value);
                
                // Check if this block exists in our blocks directory
                if ($block_name && in_array($block_name, $available_blocks)) {
                    $block_path = get_template_directory() . '/blocks/' . $block_name;
                    
                    // Use a delayed action to ensure ACF has finished saving
                    add_action('acf/save_post', function() use ($field_group, $block_path) {
                        blast_move_field_group_to_block($field_group, $block_path);
                    }, 25);
                    
                    return; // Found block rule, no need to continue
                }
            }
        }
    }
}

// Helper function to get all available blocks from blocks directory
function blast_get_available_blocks() {
    $blocks_dir = get_template_directory() . '/blocks/';
    $available_blocks = array();
    
    if (is_dir($blocks_dir)) {
        $block_folders = glob($blocks_dir . '*', GLOB_ONLYDIR);
        foreach ($block_folders as $block_folder) {
            $block_name = basename($block_folder);
            // Skip excluded blocks (like 'sample')
            if (!in_array($block_name, array('sample'))) {
                $available_blocks[] = $block_name;
            }
        }
    }
    
    return $available_blocks;
}

// Helper function to extract block name from any namespace
function blast_extract_block_name($block_value) {
    // Handle different block naming conventions
    if (strpos($block_value, '/') !== false) {
        // Any namespace: namespace/block-name
        $parts = explode('/', $block_value);
        return end($parts); // Get the last part (block name)
    }
    
    // If no namespace, return as-is
    return $block_value;
}

// Function to move field group JSON to block directory
function blast_move_field_group_to_block($field_group, $block_path) {
    $field_group_key = $field_group['key'];
    $default_json = get_template_directory() . '/acf-json/group_' . $field_group_key . '.json';
    $block_json = $block_path . '/fields.json';
    
    // Wait a moment for ACF to finish writing the file
    if (file_exists($default_json)) {
        // Copy the content to the block directory as fields.json
        $json_content = file_get_contents($default_json);
        if ($json_content) {
            file_put_contents($block_json, $json_content);
            // Remove the file from default location
            unlink($default_json);
            error_log("Moved field group to block: {$block_path}/fields.json");
        }
    }
}

// Additional cleanup function to catch any missed files
add_action('shutdown', 'blast_cleanup_block_field_groups');
function blast_cleanup_block_field_groups() {
    $acf_json_dir = get_template_directory() . '/acf-json/';
    
    if (!is_dir($acf_json_dir)) {
        return;
    }
    
    // Get all available blocks
    $available_blocks = blast_get_available_blocks();
    $json_files = glob($acf_json_dir . 'group_*.json');
    
    foreach ($json_files as $json_file) {
        $content = file_get_contents($json_file);
        if ($content) {
            $field_group = json_decode($content, true);
            
            if ($field_group && isset($field_group['location'])) {
                foreach ($field_group['location'] as $location_group) {
                    foreach ($location_group as $rule) {
                        if ($rule['param'] === 'block' && $rule['operator'] === '==') {
                            $block_value = $rule['value'];
                            $block_name = blast_extract_block_name($block_value);
                            
                            // Check if this block exists in our blocks directory
                            if ($block_name && in_array($block_name, $available_blocks)) {
                                $block_path = get_template_directory() . '/blocks/' . $block_name;
                                $block_json = $block_path . '/fields.json';
                                
                                // Move to block directory
                                if (copy($json_file, $block_json)) {
                                    unlink($json_file);
                                    error_log("Cleanup: Moved {$json_file} to {$block_json}");
                                }
                                break 2; // Break out of both loops
                            }
                        }
                    }
                }
            }
        }
    }
}

add_filter('acf/settings/load_json', 'blast_acf_json_load_point');
function blast_acf_json_load_point($paths) {
    // Remove original path (optional)
    unset($paths[0]);
    
    // Add the default acf-json path
    $paths[] = get_template_directory() . '/acf-json';
    
    // Add block-level JSON paths
    $blocks_dir = get_template_directory() . '/blocks/';
    if (is_dir($blocks_dir)) {
        $block_folders = glob($blocks_dir . '*', GLOB_ONLYDIR);
        foreach ($block_folders as $block_folder) {
            $fields_json = $block_folder . '/fields.json';
            if (file_exists($fields_json)) {
                $paths[] = $block_folder;
            }
        }
    }
    
    return $paths;
}

// Manual sync function for development - you can call this if needed
function blast_manual_sync_block_fields() {
    $acf_json_dir = get_template_directory() . '/acf-json/';
    
    if (!is_dir($acf_json_dir)) {
        return;
    }
    
    // Get all available blocks
    $available_blocks = blast_get_available_blocks();
    $json_files = glob($acf_json_dir . 'group_*.json');
    
    foreach ($json_files as $json_file) {
        $content = file_get_contents($json_file);
        if ($content) {
            $field_group = json_decode($content, true);
            
            if ($field_group && isset($field_group['location'])) {
                foreach ($field_group['location'] as $location_group) {
                    foreach ($location_group as $rule) {
                        if ($rule['param'] === 'block' && $rule['operator'] === '==') {
                            $block_value = $rule['value'];
                            $block_name = blast_extract_block_name($block_value);
                            
                            // Check if this block exists in our blocks directory
                            if ($block_name && in_array($block_name, $available_blocks)) {
                                $block_path = get_template_directory() . '/blocks/' . $block_name;
                                $block_json = $block_path . '/fields.json';
                                
                                // Copy to block directory
                                if (copy($json_file, $block_json)) {
                                    unlink($json_file);
                                    error_log("Manual sync: Moved {$json_file} to {$block_json}");
                                }
                                break 2;
                            }
                        }
                    }
                }
            }
        }
    }
}

// Uncomment the line below to run manual sync once
// add_action('init', 'blast_manual_sync_block_fields', 5);

// Debug function to see what blocks are available (useful for development)
function blast_debug_available_blocks() {
    $blocks = blast_get_available_blocks();
    error_log('Available blocks for ACF sync: ' . print_r($blocks, true));
}
// Uncomment to debug available blocks
// add_action('init', 'blast_debug_available_blocks');



function register_all_acf_blocks() {
    // Debug: Check if ACF is active
    if (!function_exists('acf_register_block_type')) {
        error_log('ACF is not active or acf_register_block_type function does not exist');
        return;
    }
    
    // Only proceed if not already registered
    if (defined('blast_BLOCKS_REGISTERED')) {
        error_log('Blocks already registered, skipping...');
        return;
    }
    
    // Prevent multiple executions
    define('blast_BLOCKS_REGISTERED', true);
    
    $blocks_dir = get_template_directory() . '/blocks/';
    
    // Check if blocks directory exists
    if (!is_dir($blocks_dir)) {
        error_log('Blocks directory does not exist: ' . $blocks_dir);
        return;
    }
    
    error_log('Starting block registration from: ' . $blocks_dir);
    
    // Auto-register all block directories except excluded ones
    $excluded_blocks = array('sample'); // Add any blocks you want to exclude
    
    // Get all directories in the blocks folder
    $block_folders = glob($blocks_dir . '*', GLOB_ONLYDIR);
    $known_blocks = array();
    
    if (!empty($block_folders)) {
        foreach ($block_folders as $block_folder) {
            $block_name = basename($block_folder);
            
            // Skip excluded blocks
            if (!in_array($block_name, $excluded_blocks)) {
                $known_blocks[] = $block_name;
            }
        }
    }
    
    foreach ($known_blocks as $block_name) {
        $block_folder = $blocks_dir . $block_name;
        $block_json = $block_folder . '/block.json';
        $helpers_php = $block_folder . '/helpers.php';
        $block_php = $block_folder . '/block.php'; // Fallback for legacy blocks
        
        // Priority 1: Use block.json if it exists (ACF V3 approach)
        if (file_exists($block_json)) {
            try {
                // Register block using block.json
                register_block_type($block_folder);
                
                // Include helpers file for any custom logic (priority)
                if (file_exists($helpers_php)) {
                    include_once $helpers_php;
                }
                // Fallback to block.php for legacy blocks
                elseif (file_exists($block_php)) {
                    include_once $block_php;
                }
                
                error_log("Registered ACF block via block.json: $block_name");
            } catch (Exception $e) {
                error_log('Error loading ACF block via JSON: ' . $block_name . ' - ' . $e->getMessage());
            }
        }
        // Fallback: Use block.php for legacy blocks
        elseif (file_exists($block_php)) {
            try {
                include_once $block_php;
                error_log("Registered ACF block via block.php: $block_name");
            } catch (Exception $e) {
                error_log('Error loading ACF block via PHP: ' . $block_name . ' - ' . $e->getMessage());
            }
        }
    }
}
add_action('init', 'register_all_acf_blocks', 20);