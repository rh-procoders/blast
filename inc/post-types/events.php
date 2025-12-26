<?php
/**
 * Register Events CPT and event_types taxonomy
 *
 * Events CPT:
 * - Has single post links: /events/{post-slug}
 * - No built-in archive (using custom page at /events/)
 *
 * event_types taxonomy:
 * - Category taxonomy for filtering (events, webinars)
 * - Internal only, no archive links
 *
 * @package blast_Wp
 */

add_action( 'init', 'blast_events_cpt' );

if ( ! function_exists( 'blast_events_cpt' ) ) :
	function blast_events_cpt() {
		$popular = 'Events';
		$singular = 'Event';

		$labels = array(
			'name'               => $popular,
			'singular_name'      => $singular,
			'add_new'            => 'Add ' . $singular,
			'all_items'          => 'All ' . $popular,
			'add_new_item'       => 'Add ' . $singular,
			'edit_item'          => 'Edit ' . $singular,
			'new_item'           => 'New ' . $singular,
			'view_item'          => 'View ' . $singular,
			'search_items'       => 'Search ' . $popular,
			'not_found'          => 'No ' . $popular . ' found',
			'not_found_in_trash' => 'No ' . $popular . ' found in trash',
			'parent_item_colon'  => 'Parent ' . $singular,
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => false, // custom page at /events/
			'publicly_queryable'  => true,  // enable single post links
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'events',
				'with_front' => false,
			),
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
			),
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-calendar-alt',
			'exclude_from_search' => false,
			'taxonomies'          => array( 'event_types' ),
		);

		register_post_type( 'events', $args );
	}
endif;

/**
 * Register event_types taxonomy
 * Hierarchical category taxonomy for filtering events
 * Internal only - no archive pages
 */
add_action( 'init', 'blast_events_taxonomy' );

if ( ! function_exists( 'blast_events_taxonomy' ) ) :
	function blast_events_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Event Types', 'Taxonomy General Name', 'blast-wp' ),
			'singular_name'              => _x( 'Event Type', 'Taxonomy Singular Name', 'blast-wp' ),
			'menu_name'                  => __( 'Event Types', 'blast-wp' ),
			'all_items'                  => __( 'All Event Types', 'blast-wp' ),
			'parent_item'                => __( 'Parent Event Type', 'blast-wp' ),
			'parent_item_colon'          => __( 'Parent Event Type:', 'blast-wp' ),
			'new_item_name'              => __( 'New Event Type', 'blast-wp' ),
			'add_new_item'               => __( 'Add New Event Type', 'blast-wp' ),
			'edit_item'                  => __( 'Edit Event Type', 'blast-wp' ),
			'update_item'                => __( 'Update Event Type', 'blast-wp' ),
			'view_item'                  => __( 'View Event Type', 'blast-wp' ),
			'separate_items_with_commas' => __( 'Separate event types with commas', 'blast-wp' ),
			'add_or_remove_items'        => __( 'Add or remove event types', 'blast-wp' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'blast-wp' ),
			'popular_items'              => __( 'Popular Event Types', 'blast-wp' ),
			'search_items'               => __( 'Search Event Types', 'blast-wp' ),
			'not_found'                  => __( 'Not Found', 'blast-wp' ),
			'no_terms'                   => __( 'No Event Types', 'blast-wp' ),
			'items_list'                 => __( 'Event Types list', 'blast-wp' ),
			'items_list_navigation'      => __( 'Event Types list navigation', 'blast-wp' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,   // no front-end archives
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'rewrite'           => false,   // no archive links
		);

		register_taxonomy( 'event_types', array( 'events' ), $args );
	}
endif;

/**
 * Disable archive links for event_types taxonomy
 * These are used for AJAX filtering only, not direct navigation
 */
add_filter( 'term_link', 'blast_disable_event_types_links', 10, 3 );

if ( ! function_exists( 'blast_disable_event_types_links' ) ) :
	function blast_disable_event_types_links( $url, $term, $taxonomy ) {
		return ( 'event_types' === $taxonomy ) ? '' : $url;
	}
endif;