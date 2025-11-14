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


/**
 * Modify CF7 Multi-Step buttons to match site button style
 * and inject progress bar
 *
 * Hooks into wpcf7_form_elements to restructure the multi-step plugin buttons
 * to use the same markup and styling as the site's standard buttons, and adds
 * a progress indicator before the fieldset wrapper
 */
function blast_customize_cf7_multistep_buttons( $form_html ) {
    // Only process if the form contains multi-step buttons
    if ( strpos( $form_html, 'cf7mls_btn' ) === false ) {
        return $form_html;
    }

    // Use DOMDocument for reliable HTML parsing
    libxml_use_internal_errors( true );
    $dom = new DOMDocument();
    $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $form_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
    $xpath = new DOMXPath( $dom );

    // Inject progress bar before fieldset-cf7mls-wrapper
    blast_inject_progress_bar( $xpath, $dom );

    // Find all Next buttons (cf7mls_next)
    $next_buttons = $xpath->query( '//button[contains(@class, "cf7mls_next")]' );
    foreach ( $next_buttons as $button ) {
        blast_restructure_multistep_button( $button, 'next', $dom );
        blast_wrap_button_with_block_div( $button, $dom );
    }

    // Find all Back buttons (cf7mls_back)
    $back_buttons = $xpath->query( '//button[contains(@class, "cf7mls_back")]' );
    foreach ( $back_buttons as $button ) {
        blast_restructure_multistep_button( $button, 'back', $dom );
        blast_wrap_button_with_block_div( $button, $dom );
    }

    // Get the modified HTML
    $modified_html = $dom->saveHTML();

    // Clean up DOMDocument artifacts
    $modified_html = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( [ '<?xml encoding="utf-8" ?>', '<html><body>', '</body></html>' ], '', $modified_html ) );

    libxml_clear_errors();

    return $modified_html;
}
add_filter( 'wpcf7_form_elements', 'blast_customize_cf7_multistep_buttons', 20 );

/**
 * Restructure a single multi-step button to match site button style
 *
 * @param DOMElement $button The button element to modify
 * @param string $button_type 'next' or 'back'
 * @param DOMDocument $dom The DOM document
 * @throws DOMException
 */
function blast_restructure_multistep_button( $button, $button_type, $dom ) {
    // Get button text (first text node)
    $button_text = '';
    foreach ( $button->childNodes as $node ) {
        if ( $node->nodeType === XML_TEXT_NODE ) {
            $button_text = trim( $node->nodeValue );
            break;
        }
    }

    // Default to "Next" or "Back" if empty
    if ( empty( $button_text ) ) {
        $button_text = ( $button_type === 'next' ) ? 'Next' : 'Back';
    }

    // Add site button classes (remove action-button, add btn and has-arrow-icon)
    $existing_classes = $button->getAttribute( 'class' );
    $existing_classes = str_replace( 'action-button', '', $existing_classes );
    $existing_classes = trim( preg_replace( '/\s+/', ' ', $existing_classes ) ); // Clean up extra spaces
    $button->setAttribute( 'class', $existing_classes . ' btn has-arrow-icon' );

    // Clear existing button content
    while ( $button->firstChild ) {
        $button->removeChild( $button->firstChild );
    }

    // Create button-text span
    $text_span = $dom->createElement( 'span' );
    $text_span->setAttribute( 'class', 'button-text' );
    $text_span->setAttribute( 'data-hover-text', $button_text );
    $text_span->nodeValue = $button_text;

    // Create arrow wrapper span
    $arrow_wrapper = $dom->createElement( 'span' );
    $arrow_wrapper->setAttribute( 'class', 'button-arrow-wrapper' );

    // Create SVG element with icon sprite reference
    $svg = $dom->createElement( 'svg' );
    $svg->setAttribute( 'class', 'svg-icon icon-arrow-right' );
    $svg->setAttribute( 'width', '14' );
    $svg->setAttribute( 'height', '10' );

    // Create use element for sprite reference
    $use = $dom->createElement( 'use' );
    $sprite_url = get_template_directory_uri() . '/img/general/icon-sprites.svg?ver=' . THEME_VERSION . '#icon-arrow-right';
    $use->setAttribute( 'xlink:href', $sprite_url );

    $svg->appendChild( $use );
    $arrow_wrapper->appendChild( $svg );

    // Add loader img back (for Next buttons)
    if ( $button_type === 'next' ) {
        $loader_img = $dom->createElement( 'img' );
        $loader_img->setAttribute( 'src', plugins_url( 'cf7-multi-step/assets/frontend/img/loader.svg' ) );
        $loader_img->setAttribute( 'alt', 'Step Loading' );
        $loader_img->setAttribute( 'style', 'display: none;' );
        $loader_img->setAttribute( 'class', 'cf7mls-loader' );

        // Append elements to button
        $button->appendChild( $text_span );
        $button->appendChild( $arrow_wrapper );
        $button->appendChild( $loader_img );
    } else {
        // Back button - no loader
        $button->appendChild( $text_span );
        $button->appendChild( $arrow_wrapper );
    }
}

/**
 * Wrap a button element with a div.wp-block-button wrapper
 *
 * @param DOMElement $button The button element to wrap
 * @param DOMDocument $dom The DOM document
 * @throws DOMException
 */
function blast_wrap_button_with_block_div( $button, $dom ) {
    // Create wrapper div with block button classes
    $wrapper = $dom->createElement( 'div' );
    $wrapper->setAttribute( 'class', 'wp-block-button is-style-outline is-style-outline--1' );

    // Get button's parent node
    $parent = $button->parentNode;

    // Insert wrapper before button
    $parent->insertBefore( $wrapper, $button );

    // Move button into wrapper
    $wrapper->appendChild( $button );
}

/**
 * Inject progress bar before the fieldset-cf7mls-wrapper
 *
 * Creates a progress indicator showing "Step X of Y" where X is the current step
 * and Y is the total number of steps (fieldsets)
 *
 * @param DOMXPath $xpath The XPath object for querying
 * @param DOMDocument $dom The DOM document
 * @throws DOMException
 */
function blast_inject_progress_bar( $xpath, $dom ) {
    // Find the fieldset-cf7mls-wrapper div
    $wrapper_divs = $xpath->query( '//div[contains(@class, "fieldset-cf7mls-wrapper")]' );

    if ( $wrapper_divs->length === 0 ) {
        return; // No wrapper found, exit early
    }

    $wrapper = $wrapper_divs->item( 0 );

    // Count total fieldsets inside wrapper
    $fieldsets = $xpath->query( './/fieldset[contains(@class, "fieldset-cf7mls")]', $wrapper );
    $total_steps = $fieldsets->length;

    if ( $total_steps === 0 ) {
        return; // No fieldsets found, exit early
    }

    // Determine current step (find which fieldset has cf7mls_current_fs class)
    $current_step = 1;
    for ( $i = 0; $i < $fieldsets->length; $i++ ) {
        $fieldset = $fieldsets->item( $i );
        $classes = $fieldset->getAttribute( 'class' );
        if ( strpos( $classes, 'cf7mls_current_fs' ) !== false ) {
            $current_step = $i + 1;
            break;
        }
    }

    // Calculate initial percentage
    $percentage = ( $total_steps > 0 ) ? ( $current_step / $total_steps ) * 100 : 0;

    // Create progress bar container
    $progress_bar = $dom->createElement( 'div' );
    $progress_bar->setAttribute( 'class', 'cf7mls-progress-bar' );

    // Create progress bar text element
    $progress_text = $dom->createElement( 'div' );
    $progress_text->setAttribute( 'class', 'cf7mls-progress-bar__text' );

    // Build text: "Step <span class="cf7mls-progress-bar__current">1</span> of <span class="cf7mls-progress-bar__total">3</span>"
    $step_text = $dom->createTextNode( 'Step ' );
    $progress_text->appendChild( $step_text );

    $current_span = $dom->createElement( 'span' );
    $current_span->setAttribute( 'class', 'cf7mls-progress-bar__current' );
    $current_span->nodeValue = strval( $current_step );
    $progress_text->appendChild( $current_span );

    $of_text = $dom->createTextNode( ' of ' );
    $progress_text->appendChild( $of_text );

    $total_span = $dom->createElement( 'span' );
    $total_span->setAttribute( 'class', 'cf7mls-progress-bar__total' );
    $total_span->nodeValue = strval( $total_steps );
    $progress_text->appendChild( $total_span );

    // Append text to progress bar
    $progress_bar->appendChild( $progress_text );

    // Create visual progress bar track
    $progress_track = $dom->createElement( 'div' );
    $progress_track->setAttribute( 'class', 'cf7mls-progress-bar__track' );

    // Create progress bar fill
    $progress_fill = $dom->createElement( 'div' );
    $progress_fill->setAttribute( 'class', 'cf7mls-progress-bar__fill' );
    $progress_fill->setAttribute( 'style', 'width: ' . number_format( $percentage, 2, '.', '' ) . '%' );
    $progress_fill->setAttribute( 'data-progress', strval( $current_step ) );

    // Append fill to track
    $progress_track->appendChild( $progress_fill );

    // Append track to progress bar
    $progress_bar->appendChild( $progress_track );

    // Insert progress bar before the wrapper
    $wrapper->parentNode->insertBefore( $progress_bar, $wrapper );
}
