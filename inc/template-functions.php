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


/**
 * Featured star column for default Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BS_FEATURED_META_KEY', 'bs-post__is-featured' );

/**
 * Add "star" column to Posts list table.
 */
add_filter( 'manage_post_posts_columns', function ( $columns ) {

    $new = [];

    foreach ($columns as $key => $label) {
        $new[$key] = $label;

        // Inject our column right after the title column.
        if ( 'title' === $key ) {
            $new['bs_featured'] = '★';
        }
    }

    // Fallback in case "title" column key changes somehow.
    if ( ! isset( $new['bs_featured'] ) ) {
        $new['bs_featured'] = '★';
    }

    return $new;
} );

if (function_exists('wpcf7_autop_or_not')) {
    /**
     * Contact 7
     * Remove auto p-tags
     *
     * ref: https://pineco.de/snippets/remove-p-tag-from-contact-form-7/
     */
    add_filter('wpcf7_autop_or_not', '__return_false');
}


/**
 * Modify TOC return to add a top link
 */
function bs_add_custom_toc_link( $html )
{
    // Get the current post ID
    $post_id = get_the_ID();

    // Check if the post type is "leader_interview" AND the assigned template is "single-leader_interview.php"
    if ( is_singular( 'post' ) ) {

        // Define the custom link
        $custom_link = '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-0" href="#post-' . esc_attr( $post_id ) . '" title="Top">' . __( "Intro", "blast-2025" ) . '</a></li>';

        // Prepend the custom link to the existing TOC
        $html = $custom_link . $html;
    }

    return $html;
}

// Apply the filter only on the correct template
add_filter( 'ez_toc_add_custom_links', 'bs_add_custom_toc_link' );

/**
 * Render the star button in our custom column.
 */
add_action( 'manage_post_posts_custom_column', function ( $column, $post_id ) {

    if ( 'bs_featured' !== $column ) {
        return;
    }

    // Read ACF true/false value – stored as '0' or '1'
    $is_featured = (bool)get_post_meta( $post_id, BS_FEATURED_META_KEY, true );

    $nonce      = wp_create_nonce( 'bs_toggle_post_featured_' . $post_id );
    $icon_class = $is_featured ? 'dashicons-star-filled' : 'dashicons-star-empty';
    $aria_label = $is_featured
        ? __( 'Unmark as featured', 'blast-2025' )
        : __( 'Mark as featured', 'blast-2025' );

    echo '<button
        type="button"
        class="bs-featured-toggle button-link"
        data-post-id="' . esc_attr( $post_id ) . '"
        data-nonce="' . esc_attr( $nonce ) . '"
        aria-label="' . esc_attr( $aria_label ) . '"
        title="' . esc_attr( $aria_label ) . '"
    >';
    echo '<span class="dashicons ' . esc_attr( $icon_class ) . '"></span>';
    echo '</button>';
}, 10, 2 );

/**
 * Make the column narrow + center aligned only on Posts list screen.
 */
add_action( 'admin_head-edit.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || 'edit-post' !== $screen->id ) {
        return;
    }
    ?>
    <style>
        .column-bs_featured {
            width: 60px;
            text-align: center!important;
        }

        .column-bs_featured .bs-featured-toggle .dashicons {
            font-size: 18px;
        }

        .column-bs_featured .bs-featured-toggle {
            cursor: pointer;
        }
    </style>
    <?php
} );

/**
 * AJAX: toggle the ACF "Is Featured" true/false field.
 */
add_action( 'wp_ajax_bs_toggle_post_featured', function () {

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => 'No permission.' ), 403 );
    }

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    $nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

    if ( ! $post_id || ! $nonce ) {
        wp_send_json_error( array( 'message' => 'Missing data.' ), 400 );
    }

    if ( ! wp_verify_nonce( $nonce, 'bs_toggle_post_featured_' . $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce.' ), 403 );
    }

    $current = (bool)get_post_meta( $post_id, BS_FEATURED_META_KEY, true );
    $new     = $current ? 0 : 1;

    // Update ACF meta; true_false expects '1' or '0'.
    update_post_meta( $post_id, BS_FEATURED_META_KEY, $new );

    wp_send_json_success( array(
        'post_id'     => $post_id,
        'is_featured' => (bool)$new,
    ) );
} );
