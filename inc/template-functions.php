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
        blast_wrap_button_with_block_div( $button, $dom, 'next' );
    }

    // Find all Back buttons (cf7mls_back)
    $back_buttons = $xpath->query( '//button[contains(@class, "cf7mls_back")]' );
    foreach ( $back_buttons as $button ) {
        blast_restructure_multistep_button( $button, 'back', $dom );
        blast_wrap_button_with_block_div( $button, $dom );
    }

    // Find and convert Submit inputs to styled buttons
    $submit_inputs = $xpath->query( '//input[contains(@class, "wpcf7-submit")]' );
    foreach ( $submit_inputs as $input ) {
        blast_convert_submit_to_button( $input, $dom );
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
 * @param string $button_type 'next', 'back', or 'submit'
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

    // Default to appropriate text if empty
    if ( empty( $button_text ) ) {
        if ( $button_type === 'next' ) {
            $button_text = 'Next';
        } elseif ( $button_type === 'back' ) {
            $button_text = 'Back';
        } else {
            $button_text = 'Submit';
        }
    }

    // Add site button classes
    $existing_classes = $button->getAttribute( 'class' );
    $existing_classes = str_replace( 'action-button', '', $existing_classes );
    $existing_classes = trim( preg_replace( '/\s+/', ' ', $existing_classes ) ); // Clean up extra spaces

    // Submit buttons get different classes than Next/Back
    if ( $button_type === 'submit' ) {
        $button->setAttribute( 'class', $existing_classes . ' btn has-dark-blue-background-color has-background wp-element-button has-arrow-icon' );
    } else {
        $button->setAttribute( 'class', $existing_classes . ' btn has-arrow-icon' );
    }

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

    // Append elements to button (spinner will be added outside button by wrapper function)
    $button->appendChild( $text_span );
    $button->appendChild( $arrow_wrapper );
}

/**
 * Wrap a button element with a div.wp-block-button wrapper
 *
 * @param DOMElement $button The button element to wrap
 * @param DOMDocument $dom The DOM document
 * @param string $button_type Optional. The button type ('submit', 'next', 'back'). Default empty.
 * @throws DOMException
 */
function blast_wrap_button_with_block_div( $button, $dom, $button_type = '' ) {
    // Create wrapper div with block button classes
    $wrapper = $dom->createElement( 'div' );

    // Submit buttons use plain wrapper, Next/Back use outline style
    if ( $button_type === 'submit' ) {
        $wrapper->setAttribute( 'class', 'wp-block-button' );
    } else {
        $wrapper->setAttribute( 'class', 'wp-block-button is-style-outline is-style-outline--1' );
    }

    // Get button's parent node
    $parent = $button->parentNode;

    // Insert wrapper before button
    $parent->insertBefore( $wrapper, $button );

    // Move button into wrapper
    $wrapper->appendChild( $button );

    // Add CF7 spinner as sibling after button (only for Next buttons)
    // Note: CF7 already adds its own spinner for Submit buttons, so we skip those
    if ( $button_type === 'next' ) {
        $spinner = $dom->createElement( 'span' );
        $spinner->setAttribute( 'class', 'wpcf7-spinner' );
        $wrapper->appendChild( $spinner );
    }
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

/**
 * Convert CF7 submit input to a styled button element
 *
 * Takes the default CF7 submit input and converts it to a button element,
 * then applies the same styling as Next/Back buttons
 *
 * @param DOMElement $input The input element to convert
 * @param DOMDocument $dom The DOM document
 * @throws DOMException
 */
function blast_convert_submit_to_button( $input, $dom ) {
    // Get the submit button text from value attribute
    $button_text = $input->getAttribute( 'value' );
    if ( empty( $button_text ) ) {
        $button_text = 'Submit';
    }

    // Get existing classes from input
    $input_classes = $input->getAttribute( 'class' );

    // Create new button element
    $button = $dom->createElement( 'button' );
    $button->setAttribute( 'type', 'submit' );

    // Transfer classes and add our custom classes
    $button->setAttribute( 'class', $input_classes . ' btn has-arrow-icon' );

    // Copy any data attributes or other relevant attributes
    if ( $input->hasAttribute( 'id' ) ) {
        $button->setAttribute( 'id', $input->getAttribute( 'id' ) );
    }
    if ( $input->hasAttribute( 'name' ) ) {
        $button->setAttribute( 'name', $input->getAttribute( 'name' ) );
    }

    // Set button text as text node temporarily (will be restructured next)
    $button->nodeValue = $button_text;

    // Replace input with button in DOM
    $input->parentNode->replaceChild( $button, $input );

    // Now apply the same restructuring as Next/Back buttons
    blast_restructure_multistep_button( $button, 'submit', $dom );
    blast_wrap_button_with_block_div( $button, $dom, 'submit' );
}

/**
 * Business Email Validation Handler for Contact Form 7
 *
 * Validates email addresses to ensure users submit business/corporate emails
 * by blocking free email providers. Uses Levenshtein distance to catch typos
 * and common misspellings of blocked domains.
 *
 * @package blast-2025
 */
class Blast_Contact_Forms_Handler {

    /**
     * List of blocked free email domains
     *
     * @var array
     */
    private $blocked_domains = array(
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com',
        'mail.com', 'protonmail.com', 'zoho.com', 'yandex.com', 'gmx.com', 'inbox.com',
        'live.com', 'msn.com', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.de', 'yahoo.es',
        'yahoo.it', 'yahoo.ca', 'yahoo.com.au', 'yahoo.co.in', 'yahoo.com.br',
        'hotmail.co.uk', 'hotmail.fr', 'hotmail.de', 'hotmail.es', 'hotmail.it',
        'outlook.fr', 'outlook.de', 'outlook.es', 'outlook.it', 'outlook.co.uk',
        'googlemail.com', 'me.com', 'mac.com', 'gmx.de', 'gmx.net', 'web.de',
        't-online.de', 'freenet.de', 'arcor.de', 'mail.ru', 'ya.ru', 'yandex.ru',
        'qq.com', '163.com', '126.com', 'sina.com', 'sohu.com', 'yeah.net',
        'rediffmail.com', 'fastmail.com', 'hushmail.com', 'tutanota.com',
        'rocketmail.com', 'att.net', 'sbcglobal.net', 'verizon.net', 'comcast.net',
        'bellsouth.net', 'charter.net', 'cox.net', 'earthlink.net', 'juno.com',
        'netzero.net', 'optonline.net', 'windstream.net', 'free.fr', 'orange.fr',
        'laposte.net', 'wanadoo.fr', 'sfr.fr', 'aliceadsl.fr', 'neuf.fr',
        'libero.it', 'virgilio.it', 'alice.it', 'tin.it', 'tiscali.it',
        'btinternet.com', 'virginmedia.com', 'sky.com', 'talktalk.net', 'blueyonder.co.uk',
        'ntlworld.com', 'tiscali.co.uk', 'freeserve.co.uk', 'orange.net', 'wanadoo.co.uk',
        'telstra.com', 'bigpond.com', 'optusnet.com.au', 'westnet.com.au', 'aapt.net.au',
        'telus.net', 'shaw.ca', 'rogers.com', 'sympatico.ca', 'videotron.ca',
        'bol.com.br', 'uol.com.br', 'terra.com.br', 'ig.com.br', 'globo.com',
        'mailinator.com', 'guerrillamail.com', 'temp-mail.org', '10minutemail.com',
        'throwaway.email', 'maildrop.cc', 'tempmail.com', 'getnada.com',
        'prodigy.net', 'openweb.net.nz', 'xtra.co.nz', 'paradise.net.nz', 'slingshot.co.nz',
        'seznam.cz', 'centrum.cz', 'post.cz', 'email.cz', 'atlas.cz',
        'wp.pl', 'o2.pl', 'interia.pl', 'onet.pl', 'gazeta.pl',
        'rambler.ru', 'bk.ru', 'inbox.ru', 'list.ru', 'lenta.ru',
        'hanmail.net', 'naver.com', 'daum.net', 'nate.com', 'korea.com',
        'sapo.pt', 'clix.pt', 'netcabo.pt', 'iol.pt', 'mail.pt',
        'bluewin.ch', 'hispeed.ch', 'sunrise.ch', 'swissonline.ch', 'vtxmail.ch',
        'home.nl', 'hetnet.nl', 'planet.nl', 'quicknet.nl', 'zonnet.nl',
        'telenet.be', 'skynet.be', 'pandora.be', 'proximus.be', 'scarlet.be',
        'bredband.net', 'spray.se', 'telia.com', 'passagen.se', 'swipnet.se',
        'chello.at', 'aon.at', 'kabsi.at', 'liwest.at', 'a1.net',
        'elisa.fi', 'kolumbus.fi', 'luukku.com', 'saunalahti.fi', 'suomi24.fi',
        'jubii.dk', 'mail.dk', 'ofir.dk', 'post.tele.dk', 'tdcadsl.dk',
        'online.no', 'frisurf.no', 'broadpark.no', 'start.no', 'chello.no',
        'ireland.com', 'eircom.net', 'indigo.ie', 'vodafone.ie', 'o2.ie',
        'ono.com', 'ya.com', 'movistar.es', 'jazztel.es', 'uni2.es',
        'club-internet.fr', 'caramail.com', 'voila.fr', 'tiscali.fr', 'bbox.fr',
        'arcor.de', 'gmx.at', 'gmx.ch', 'gmx.fr', 'gmx.es',
        'tele2.nl', 'xs4all.nl', 'ziggo.nl', 'upcmail.nl', 'kpnmail.nl',
        'iol.ie', 'poczta.onet.pl', 'pocztowy.pl', 'tlen.pl', 'vp.pl',
        'freemail.hu', 'citromail.hu', 'index.hu', 'indamail.hu', 'mailbox.hu',
        'abv.bg', 'mail.bg', 'dir.bg', 'gbg.bg', 'gyuvetch.bg',
        'azet.sk', 'atlas.sk', 'centrum.sk', 'zoznam.sk', 'post.sk',
        'volny.cz', 'tiscali.cz', 'quick.cz', 'nextra.cz', 'iol.cz',
        'freemail.gr', 'in.gr', 'otenet.gr', 'forthnet.gr', 'flash.net',
        'mynet.com', 'superonline.com', 'turk.net', 'ttnet.net.tr', 'ttmail.com',
        'ismail.net.tr', 'hotmail.com.tr', 'yahoo.com.tr', 'gmail.com.tr', 'ymail.com',
        'rr.com', 'roadrunner.com', 'twc.com', 'cfl.rr.com', 'kc.rr.com',
        'nc.rr.com', 'rochester.rr.com', 'san.rr.com', 'sc.rr.com', 'wi.rr.com',
        'frontiernet.net', 'mediacombb.net', 'toast.net', 'wowway.com', 'rcn.com',
        'metrocast.net', 'snet.net', 'ptd.net', 'epix.net', 'embarqmail.com',
        'centurylink.net', 'centurytel.net', 'q.com', 'mchsi.com', 'suddenlink.net',
        'cable.comcast.com', 'comcast.com', 'comporium.net', 'consolidatedcomm.net', 'cfl.rr.com',
        'cablevision.net', 'optimum.net', 'optonline.net', 'optonline.com', 'tampabay.rr.com',
        'insight.rr.com', 'bright.net', 'cinci.rr.com', 'neo.rr.com', 'hawaii.rr.com',
        'maine.rr.com', 'midsouth.rr.com', 'triad.rr.com', 'austin.rr.com', 'satx.rr.com',
        'elp.rr.com', 'prodigy.net.mx', 'yahoo.com.mx', 'hotmail.com.mx', 'outlook.com.mx',
        'live.com.mx', 'msn.com.mx', 'gmail.com.mx', 'uninet.net.mx', 'prodigy.net',
        'claro.net.ar', 'fibertel.com.ar', 'speedy.com.ar', 'ciudad.com.ar', 'arnet.com.ar',
        'yahoo.com.ar', 'hotmail.com.ar', 'outlook.com.ar', 'gmail.com.ar', 'live.com.ar',
        'vtr.net', 'tie.cl', 'terra.cl', 'yahoo.cl', 'hotmail.cl',
        'outlook.cl', 'gmail.cl', 'live.cl', 'une.net.co', 'etb.net.co',
        'emcali.net.co', 'cable.net.co', 'cableunion.net.co', 'yahoo.com.co', 'hotmail.com.co',
        'outlook.com.co', 'gmail.com.co', 'live.com.co', 'cantv.net', 'intercable.net.ve',
        'netuno.net.ve', 'supercable.net.ve', 'yahoo.com.ve', 'hotmail.com.ve', 'outlook.com.ve',
        'gmail.com.ve', 'live.com.ve', 'speedy.com.pe', 'terra.com.pe', 'yahoo.com.pe',
        'hotmail.com.pe', 'outlook.com.pe', 'gmail.com.pe', 'live.com.pe', 'anteldata.net.uy',
        'adinet.com.uy', 'yahoo.com.uy', 'hotmail.com.uy', 'outlook.com.uy', 'gmail.com.uy',
        'live.com.uy', 'entelchile.net', 'telmex.net.co', 'etb.net', 'emtelco.net.co',
        'movistar.com.co', 'claro.com.co', 'avantel.net.co', 'metropluscr.com', 'racsa.co.cr',
        'ice.co.cr', 'yahoo.com.cr', 'hotmail.com.cr', 'outlook.com.cr', 'gmail.com.cr',
        'live.com.cr', 'cwpanama.net', 'cwp.net.pa', '+movil.com.pa', 'yahoo.com.pa',
        'hotmail.com.pa', 'outlook.com.pa', 'gmail.com.pa', 'live.com.pa', 'cotas.com.bo',
        'entel.bo', 'viva.com.bo', 'yahoo.com.bo', 'hotmail.com.bo', 'outlook.com.bo',
        'gmail.com.bo', 'live.com.bo', 'antel.com.uy', 'claro.com.uy', 'movistar.com.uy',
        'coopservitel.com.py', 'tigo.com.py', 'claro.com.py', 'yahoo.com.py', 'hotmail.com.py',
        'outlook.com.py', 'gmail.com.py', 'live.com.py', 'movistar.com.ec', 'cnt.net.ec',
        'claro.com.ec', 'etapaonline.net.ec', 'yahoo.com.ec', 'hotmail.com.ec', 'outlook.com.ec',
        'gmail.com.ec', 'live.com.ec', 'kolbi.cr', 'kölbi.cr', 'telecable.cr',
        'cabletica.com', 'cablecolor.cr', 'tigo.com.gt', 'claro.com.gt', 'movistar.com.gt',
        'yahoo.com.gt', 'hotmail.com.gt', 'outlook.com.gt', 'gmail.com.gt', 'live.com.gt',
        'tigo.com.hn', 'claro.com.hn', 'hondutel.hn', 'yahoo.com.hn', 'hotmail.com.hn',
        'outlook.com.hn', 'gmail.com.hn', 'live.com.hn', 'tigo.com.sv', 'claro.com.sv',
        'digicel.sv', 'yahoo.com.sv', 'hotmail.com.sv', 'outlook.com.sv', 'gmail.com.sv',
        'live.com.sv', 'claro.com.ni', 'movistar.com.ni', 'yahoo.com.ni', 'hotmail.com.ni',
        'outlook.com.ni', 'gmail.com.ni', 'live.com.ni', 'cable-modem.net', 'cableonda.net',
        'cablecolor.com', 'amnet.co.cr', 'cablevisionsacr.com', 'tigo.com.co', 'une.net',
        'emcali.com.co', 'epm.net.co', 'metrotel.net.co', 'movistar.com.pe', 'claro.com.pe',
        'bitel.com.pe', 'entel.pe', 'yahoo.es.pe', 'nextel.net.ar', 'telecentro.net.ar',
        'movistar.com.ar', 'claro.com.ar', 'personal.com.ar', 'telefonica.net.ar', 'iplan.com.ar',
        'cablehogar.net.ar', 'cablevisionfibertel.com.ar', 'gigared.net.ar', 'favanet.com.ar', 'telecom.net.ar',
        'movistar.cl', 'claro.cl', 'entel.cl', 'wom.cl', 'gtdmanquehue.cl',
        'mundo.cl', 'tutopia.cl', 'netglobalis.net', 'firstcom.cl', 'telmex.cl',
        'movistar.com.uy', 'claro.uy', 'dedicado.com.uy', 'montevideo.com.uy', 'movistar.com.ve',
        'cantv.com.ve', 'digitel.com.ve', 'movilnet.com.ve', 'inter.net.ve', 'cvnet.net.ve',
        'supercable.com.ve', 'aba.net.ve', 'telcel.net.ve', 'movistar.net.ve', 'gmail.co.za',
        'yahoo.co.za', 'hotmail.co.za', 'outlook.co.za', 'live.co.za', 'webmail.co.za',
        'mweb.co.za', 'iafrica.com', 'vodamail.co.za', 'telkomsa.net', 'absamail.co.za',
        'mtn.co.za', 'vodacom.co.za', 'cell-c.co.za', 'telkom.net', 'lando.co.za',
        'axxess.co.za', 'webafrica.co.za', 'cybersmart.co.za', 'imaginet.co.za', 'dotsure.co.za',
        'gmail.com.ng', 'yahoo.com.ng', 'hotmail.com.ng', 'outlook.com.ng', 'live.com.ng',
        'ymail.com.ng', 'aol.com.ng', 'mail.com.ng', 'zoho.com.ng', 'fastmail.com.ng',
        'gmail.co.ke', 'yahoo.co.ke', 'hotmail.co.ke', 'outlook.co.ke', 'live.co.ke',
        'safaricom.co.ke', 'orange.co.ke', 'airtel.co.ke', 'telkom.co.ke', 'jambo.co.ke',
        'gmail.com.eg', 'yahoo.com.eg', 'hotmail.com.eg', 'outlook.com.eg', 'live.com.eg',
        'tedata.net.eg', 'vodafone.com.eg', 'link.net.eg', 'noor.net.eg', 'soficom.com.eg',
        'gmail.co.il', 'yahoo.co.il', 'hotmail.co.il', 'outlook.co.il', 'walla.co.il',
        'walla.com', 'netvision.net.il', '012.net.il', 'bezeqint.net', 'zahav.net.il',
        'gmail.com.sa', 'yahoo.com.sa', 'hotmail.com.sa', 'outlook.com.sa', 'live.com.sa',
        'stc.com.sa', 'mobily.com.sa', 'zain.com.sa', 'saudi.net.sa', 'jawwy.tv',
        'gmail.ae', 'yahoo.ae', 'hotmail.ae', 'outlook.ae', 'live.ae',
        'eim.ae', 'emirates.net.ae', 'etisalat.ae', 'du.ae', 'hotmail.com.ae',
        'gmail.com.au', 'yahoo.com.au', 'hotmail.com.au', 'outlook.com.au', 'live.com.au',
        'bigpond.net.au', 'optusnet.com.au', 'tpg.com.au', 'iinet.net.au', 'westnet.com.au',
        'dodo.com.au', 'internode.on.net', 'adam.com.au', 'aapt.net.au', 'eftel.com',
        'gmail.co.nz', 'yahoo.co.nz', 'hotmail.co.nz', 'outlook.co.nz', 'live.co.nz',
        'xtra.co.nz', 'clear.net.nz', 'paradise.net.nz', 'slingshot.co.nz', 'orcon.net.nz',
        'snap.net.nz', 'ihug.co.nz', 'vodafone.co.nz', '2degrees.nz', 'inspire.net.nz',
        'gmail.co.jp', 'yahoo.co.jp', 'hotmail.co.jp', 'outlook.co.jp', 'live.jp',
        'ezweb.ne.jp', 'docomo.ne.jp', 'softbank.ne.jp', 'i.softbank.jp', 'nifty.com',
        'biglobe.ne.jp', 'so-net.ne.jp', 'ocn.ne.jp', 'plala.or.jp', 'dion.ne.jp',
        'gmail.co.kr', 'yahoo.co.kr', 'hotmail.co.kr', 'outlook.co.kr', 'live.co.kr',
        'naver.com', 'hanmail.net', 'daum.net', 'nate.com', 'korea.com',
        'gmail.com.cn', 'yahoo.com.cn', 'hotmail.com.cn', 'outlook.com.cn', 'live.com.cn',
        'qq.com', '163.com', '126.com', 'sina.com', 'sohu.com',
        'yeah.net', '188.com', '139.com', 'wo.com.cn', 'vip.sina.com',
        'gmail.com.sg', 'yahoo.com.sg', 'hotmail.com.sg', 'outlook.com.sg', 'live.com.sg',
        'singnet.com.sg', 'starhub.net.sg', 'pacific.net.sg', 'mail.com.sg', 'fastmail.com.sg',
        'gmail.com.my', 'yahoo.com.my', 'hotmail.com.my', 'outlook.com.my', 'live.com.my',
        'tm.net.my', 'streamyx.com', 'maxis.com.my', 'celcom.com.my', 'digi.com.my',
        'gmail.co.th', 'yahoo.co.th', 'hotmail.co.th', 'outlook.co.th', 'live.co.th',
        'hotmail.com.th', 'sanook.com', 'thaimail.com', 'truemail.co.th', 'gmx.co.th',
        'gmail.com.ph', 'yahoo.com.ph', 'hotmail.com.ph', 'outlook.com.ph', 'live.com.ph',
        'ymail.com.ph', 'rocketmail.com.ph', 'aol.com.ph', 'pldt.net', 'globe.com.ph',
        'gmail.co.id', 'yahoo.co.id', 'hotmail.co.id', 'outlook.co.id', 'live.co.id',
        'telkomsel.co.id', 'indosat.net.id', 'xl.co.id', 'cbn.net.id', 'centrin.net.id',
        'gmail.com.vn', 'yahoo.com.vn', 'hotmail.com.vn', 'outlook.com.vn', 'live.com.vn',
        'ymail.com.vn', 'vnn.vn', 'hn.vnn.vn', 'fpt.vn', 'vnpt.vn',
        'gmail.com.pk', 'yahoo.com.pk', 'hotmail.com.pk', 'outlook.com.pk', 'live.com.pk',
        'mobilink.net.pk', 'ufone.com.pk', 'zong.com.pk', 'ptcl.net.pk', 'paknet.com.pk',
        'gmail.com.bd', 'yahoo.com.bd', 'hotmail.com.bd', 'outlook.com.bd', 'live.com.bd',
        'btcl.net.bd', 'grameenphone.com', 'robi.com.bd', 'banglalink.net', 'airtel.com.bd',
        'gmail.lk', 'yahoo.lk', 'hotmail.lk', 'outlook.lk', 'live.lk',
        'sltnet.lk', 'mobitel.lk', 'dialog.lk', 'etisalat.lk', 'hutch.lk',
        'rediff.com', 'rediffmail.com', 'indiatimes.com', 'sify.com', 'in.com',
        'vsnl.net', 'bsnl.in', 'mtnl.net.in', 'sancharnet.in', 'airtelmail.in',
        'vodafone.in', 'idea.net.in', 'tatamail.com', 'jio.com', 'jiomeet.com',
    );

    /**
     * Constructor
     * Initialize hooks for email validation
     */
    public function __construct() {
        // Hook into CF7 email field validation
        add_filter( 'wpcf7_validate_email', array( $this, 'validate_email_before_sending' ), 10, 2 );
        add_filter( 'wpcf7_validate_email*', array( $this, 'validate_email_before_sending' ), 10, 2 );
    }

    /**
     * Validate email domain against blocked list
     *
     * @param WPCF7_Validation $result The validation result object
     * @param WPCF7_FormTag $tag The form tag object
     * @return WPCF7_Validation Modified validation result
     */
    public function validate_email_before_sending( $result, $tag ) {
        // Get the field name
        $name = $tag->name;

        // Get the submitted email value
        $email = isset( $_POST[ $name ] ) ? trim( $_POST[ $name ] ) : '';

        // If empty, let CF7's required validation handle it
        if ( empty( $email ) ) {
            return $result;
        }

        // Extract domain from email
        $email_parts = explode( '@', $email );
        if ( count( $email_parts ) !== 2 ) {
            return $result; // Invalid email format, let CF7's validation handle it
        }

        $domain = strtolower( trim( $email_parts[1] ) );

        // Check if domain is in blocked list (exact match)
        if ( in_array( $domain, $this->blocked_domains, true ) ) {
            $result->invalidate( $tag, __( 'Please use your corporate email address.', 'blast-2025' ) );
            return $result;
        }

        // Check for typos using Levenshtein distance (catches common misspellings)
        foreach ( $this->blocked_domains as $blocked_domain ) {
            $distance = levenshtein( $domain, $blocked_domain );

            // If distance is 1 or less, it's likely a typo (e.g., "gmial.com" vs "gmail.com")
            if ( $distance <= 1 && $distance > 0 ) {
                $result->invalidate( $tag, __( 'Please use your corporate email address.', 'blast-2025' ) );
                return $result;
            }
        }

        return $result;
    }
}

// Initialize the email validation handler
new Blast_Contact_Forms_Handler();
