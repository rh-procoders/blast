<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package blast-2025
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function blast_theme_wp_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'blast_theme_wp_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function blast_theme_wp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'blast_theme_wp_pingback_header' );


// register new Gutenberg blocks category
function add_custom_block_categories( $categories, $post ) {
	$custom_category_one = array(
		'slug' => 'blast-block',
		'title' => __( 'blast Theme Sections', 'blast-2025' ),
		'icon'  => 'admin-appearance',
	);
	array_unshift( $categories, $custom_category_one);
	return $categories;
}
add_filter( 'block_categories_all', 'add_custom_block_categories', 10, 2 );

