<?php
add_action( 'init', 'newsroom_cpt' );
/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
if( ! function_exists( 'newsroom_cpt' ) ) :
	function newsroom_cpt() {
		$popular = 'Newsroom';
		$singular = 'Newsroom Post';
		$labels = array(
			'name' => $popular,
			'singular_name' => $singular,
			'add_new' => 'Add '. $singular,
			'all_items' => 'All '.$popular,
			'add_new_item' => 'Add '.$singular,
			'edit_item' => 'Edit '. $singular,
			'new_item' => 'New '. $singular,
			'view_item' => 'View '. $singular,
			'search_items' => 'Search '. $popular,
			'not_found' => 'No '.$popular.' found',
			'not_found_in_trash' => 'No '.$popular.' found in trash',
			'parent_item_colon' => 'Parent '.$singular
			//'menu_name' => default to 'name'
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'publicly_queryable' => false,
			'query_var' => true,
			'capability_type' => 'page',
			'hierarchical' => true,
			'show_in_rest' => true,
			'supports' => array(
				'title',
				// 'editor',
				'excerpt',
				'thumbnail',
				'page-attributes',
				// 'comments',
				'revisions',
			),
			'menu_position' => 25,
			'menu_icon'          => 'dashicons-portfolio',
			'exclude_from_search' => false,
		);
		register_post_type( strtolower($popular), $args );
		// flush_rewrite_rules();

		// $taxonomies = array('location');
		// foreach($taxonomies as $taxonomy){
		// 	register_taxonomy( $taxonomy, // register custom taxonomy
		// 		strtolower($popular),
		// 		array(
		// 			'hierarchical' => true,
		// 			'show_admin_column' => true,
		// 			'show_in_rest' => true,
		// 			'labels' => array(
		// 				'name' => ucfirst(str_replace("_", " ", $taxonomy)),
		// 				'singular_name' =>ucfirst(str_replace("_", " ", $taxonomy)),
		// 				'show_admin_column' => true,
		// 			)
		// 		)
		// 	);

		// }
	}
endif;