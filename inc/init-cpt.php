<?php 
//
// Include all custom post types here (one custom post type per file)
//
add_action('after_setup_theme', 'load_custom_post_type_files');
if( !function_exists('load_custom_post_type_files') ):
function load_custom_post_type_files()
{

    $cpt_files = apply_filters('load_custom_post_type_files', array(
		'inc/post-types/newsroom',
		'inc/post-types/events'
	));

	foreach($cpt_files as $cpt_file) get_template_part($cpt_file);
}
endif;

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'new_rewrite_rules' );

// Flush your rewrite rules
function new_rewrite_rules() {
	flush_rewrite_rules();
}