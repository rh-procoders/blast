<?php
/**
 * Enqueue scripts and styles.
 */
function load_scripts() {
	// Include global style and scripts


	wp_enqueue_style( 'main', THEME_URI . '/assets/css/style.css', array(), filemtime(THEME_DIR . '/assets/css/style.css'), false );
	wp_enqueue_style( 'style', get_stylesheet_uri(), array(), filemtime(THEME_DIR . '/style.css') );

	// SimpleBar scrollbar plugin
	wp_enqueue_style( 'simplebar', THEME_URI . '/css/plugins/simplebar/simplebar.min.css' );
	wp_enqueue_script( 'simplebar', THEME_URI . '/js/plugins/simplebar/simplebar.min.js', array(), null, true );

	// Register non global style and scripts
    wp_enqueue_script( 'jquery-local', THEME_URI . '/js/plugins/jquery.min.js', '', '3.7.1', false );
	wp_register_style( 'splide-slider', THEME_URI . '/css/plugins/splide/splide.min.css' );
	wp_register_script( 'splide-slider', THEME_URI . '/js/plugins/splide/splide.min.js' );

	// Custom forms enhancements (2px caret, etc.)
	wp_register_script( 'custom-forms', THEME_URI . '/js/custom-forms.js', array(), filemtime(THEME_DIR . '/js/custom-forms.js'), true );

    wp_enqueue_script( 'lottie-player', THEME_URI . '/js/lottie-player.js', '', '1.7.1', false );
	wp_enqueue_script( 'lottie-player-interactivity', THEME_URI . '/js/lottie-interactivity.js', '', '1.7.1', false );

    // Register Spline 3D viewer library
    wp_register_script(
        'spline-viewer',
        'https://unpkg.com/@splinetool/viewer@1.10.96/build/spline-viewer.js',
        array(),
        '1.10.96',
        true
    );

    // Add module type attribute for Spline viewer
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'spline-viewer') {
            return str_replace('<script ', '<script type="module" ', $tag);
        }
        return $tag;
    }, 10, 2);

	wp_enqueue_script( 'main', THEME_URI . '/js/main.js', array(), filemtime(THEME_DIR . '/js/main.js'), true );

    // Register FAQ block script and style
    // wp_register_script(
    //     'faq-block-script',
    //     THEME_URI . '/blocks/faq/js/faq.js',
    //     array(),
    //     filemtime(THEME_DIR . '/blocks/faq/js/faq.js'),
    //     true
    // );
    // wp_register_style(
    //     'faq-block-style',
    //     THEME_URI . '/blocks/faq/css/view-style.css',
    //     array(),
    //     filemtime(THEME_DIR . '/blocks/faq/css/view-style.css')
    // );
	wp_enqueue_script( 'main', THEME_URI . '/js/main.js', array(), filemtime(THEME_DIR . '/js/main.js'), true );
	wp_enqueue_script( 'navigation', THEME_URI . '/js/navigation.js', array(), filemtime(THEME_DIR . '/js/navigation.js'), true );

    // wp_enqueue_script( 'lottie-player', get_template_directory_uri() . '/js/plugins/lottie-player.js', '', '1.7.1', false );

    // wp_enqueue_script( 'handlebars', get_template_directory_uri() . '/js/plugins/handlebars/handlebars.min.js', array(), '4.7.8', true );
    // wp_enqueue_script( 'typehead', get_template_directory_uri() . '/js/plugins/typeahead/typehead.min.js', array(), '0.11.1', true );
    // wp_enqueue_script( 'typehead-autocomplete', get_template_directory_uri() . '/js/autocomplete.js', array(), filemtime(get_template_directory() . '/js/autocomplete.js'), true );
    if( is_singular( 'post' ) ) {
        wp_enqueue_style( 'single-post',  THEME_URI . '/assets/css/single-post.css', array(), filemtime(THEME_DIR . '/assets/css/single-post.css') );
    }

    // Archive Page Styles
    wp_register_style( 'blast-archive', THEME_URI . '/assets/css/archive.css' );

    // Demo Page Assets (only on page-demo.php template)
    if ( is_page_template( 'page-demo.php' ) ) {
        wp_enqueue_style( 'page-demo-styles', THEME_URI . '/assets/css/page-demo.css', array(), filemtime(THEME_DIR . '/assets/css/page-demo.css') );
        wp_enqueue_script( 'page-demo-scripts', THEME_URI . '/js/page-demo.js', array(), filemtime(THEME_DIR . '/js/page-demo.js'), true );
    }
}
add_action( 'wp_enqueue_scripts', 'load_scripts' );




function enqueue_custom_script() {
    // Enqueue your custom JavaScript file
    wp_enqueue_script(
        'custom-script',
        THEME_URI . '/js/admin-script.js',
        array( 'wp-blocks', 'wp-editor', 'wp-components' ),
        THEME_VERSION,
        true
    );
     wp_enqueue_script( 'lottie-player', THEME_URI . '/js/lottie-player.js', '', '1.7.1', false );
}
add_action( 'enqueue_block_editor_assets', 'enqueue_custom_script' );


/**
 * Posts Editor Script
 * Used to execute setting featured posts from the Posts View screen
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    // We only want the Posts list screen.
    if ( 'edit.php' !== $hook ) {
        return;
    }

    $screen = get_current_screen();
    if ( empty( $screen ) || 'post' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_script(
        'admin-posts-script',
        THEME_URI . '/js/admin-posts-script.js',
        array( 'jquery' ), // or [] if you don't need anything
        THEME_VERSION,
        true
    );

    wp_localize_script(
        'admin-posts-script',
        'bsFeaturedPosts',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );
} );



// /wp-content/themes/blast-2025/js/plugins/lottie-player.js
