<?php
/**
 * blast-2025 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package blast-2025
 */

// Define theme constants for easy access across the theme
if (!defined('THEME_URI')) {
    define('THEME_URI', get_template_directory_uri());
}
if (!defined('THEME_DIR')) {
    define('THEME_DIR', get_template_directory());
}
if (!defined('THEME_VERSION')) {
    define('THEME_VERSION', wp_get_theme()->get('Version'));
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function blast_theme_wp_setup() {
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'header-menu-desktop' => esc_html__( 'Header Menu Desktop', 'blast-2025' ),
			'footer-menu' => esc_html__( 'Footer Menu', 'blast-2025' )
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'blast_theme_wp_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 26,
			'width'       => 176,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add theme support for editor styles
	add_theme_support( 'editor-styles' );

	// Add custom editor styles
	add_editor_style( 'assets/css/editor-style.css' );

	add_image_size( 'blog-thumbnails', 373, 124, true );


}
add_action( 'after_setup_theme', 'blast_theme_wp_setup' );

/**
 * Load theme textdomain for translations.
 *
 * This function is hooked to 'init' to comply with WordPress 6.7.0+ requirements
 * for translation loading timing.
 */
function blast_theme_wp_load_textdomain() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on blast-2025, use a find and replace
	 * to change 'blast-2025' to the name of your theme in all the template files.
	 */
	// load_theme_textdomain( 'blast-2025', get_template_directory() . '/languages' );
}
//add_action( 'init', 'blast_theme_wp_load_textdomain' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function blast_theme_wp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'blast_theme_wp_content_width', 1180 );
}
add_action( 'after_setup_theme', 'blast_theme_wp_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/scripts.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';



/**
 * Add post types
 */
require get_template_directory() . '/inc/init-cpt.php';

/**
 * Helpers Functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * AJAX Handlers
 */
require get_template_directory() . '/inc/ajax-handlers.php';

/**
 * ACF Functions and Block Registration
 */
require get_template_directory() . '/inc/acf.php';

/**
 * Enable JSON file uploads for Lottie animations
 *
 * Note: Only use for trusted users as JSON files can potentially contain malicious code
 */
function enable_json_upload($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'enable_json_upload');

/**
 * Fix MIME type detection for JSON files
 */
function check_json_filetype($data, $file, $filename, $mimes) {
    $wp_filetype = wp_check_filetype($filename, $mimes);

    if ($wp_filetype['ext'] === 'json') {
        $data['ext'] = 'json';
        $data['type'] = 'application/json';
    }

    return $data;
}
add_filter('wp_check_filetype_and_ext', 'check_json_filetype', 10, 4);

/**
 * Add JSON to the list of allowed file extensions
 */
function add_json_to_allowed_extensions($extensions) {
    $extensions[] = 'json';
    return $extensions;
}
add_filter('wp_get_allowed_extensions', 'add_json_to_allowed_extensions');

/**
 * Validate JSON files to ensure they're valid Lottie animations
 */
function validate_lottie_json_upload($file) {
    // Only validate JSON files
    if ($file['type'] !== 'application/json') {
        return $file;
    }

    // Read the JSON content
    $json_content = file_get_contents($file['tmp_name']);
    $decoded = json_decode($json_content, true);

    // Check if it's valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        $file['error'] = 'Invalid JSON file. Please upload a valid Lottie animation file.';
        return $file;
    }

    // Basic validation for Lottie format (check for required properties)
    if (!is_array($decoded) || !isset($decoded['v']) || !isset($decoded['layers'])) {
        $file['error'] = 'This JSON file does not appear to be a valid Lottie animation. Please ensure you\'re uploading a Lottie JSON file.';
        return $file;
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'validate_lottie_json_upload');


add_filter( 'gu_ignore_dot_org', '__return_true' );


/**
 * Register widget areas.
 */
function wpdocs_theme_slug_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Post Content Sidebar', 'blast-2025' ),
		'id'            => 'post-content-sidebar',
		'description'   => __( 'Widgets in this area will be shown in the right sidebar on single posts.', 'blast-2025' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Post Footer Widget', 'blast-2025' ),
		'id'            => 'post-footer-widget',
		'description'   => __( 'Widgets in this area will be shown after the author section on single posts.', 'blast-2025' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'wpdocs_theme_slug_widgets_init' );

function enqueue_fancybox() {
    // Only enqueue if the current post/page has gallery blocks
    if (is_singular() && has_blocks()) {
        $post = get_post();
        if ($post && has_block('gallery', $post)) {

			wp_enqueue_script( 'fancybox-js', THEME_URI . '/vendor/fancybox/fancybox.umd.js', '', '5.0.0', false );
			wp_enqueue_style( 'fancybox-css', THEME_URI . '/vendor/fancybox/fancybox.css', array(), '5.0.0', false );
		}
    }
}
add_action('wp_enqueue_scripts', 'enqueue_fancybox');

// Modify WordPress gallery block to add Fancybox attributes
function add_fancybox_to_gallery_block($content) {
    // Only modify on frontend and if content is not empty
    if (is_admin() || empty(trim($content))) {
        return $content;
    }

    // Check if content contains gallery block
    if (strpos($content, 'wp-block-gallery') === false) {
        return $content;
    }

    // Use DOMDocument for more reliable HTML parsing
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Find all gallery figures
    $xpath = new DOMXPath($dom);
    $galleries = $xpath->query('//figure[contains(@class, "wp-block-gallery")]');

    foreach ($galleries as $gallery) {
        // Find all image links within this gallery
        $imageLinks = $xpath->query('.//a[contains(@href, ".jpg") or contains(@href, ".jpeg") or contains(@href, ".png") or contains(@href, ".gif") or contains(@href, ".webp")]', $gallery);

        foreach ($imageLinks as $link) {
            $link->setAttribute('data-fancybox', 'gallery');
        }
    }

    // Get the modified HTML
    $modifiedContent = $dom->saveHTML();

    // Remove the XML declaration that DOMDocument adds
    $modifiedContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<?xml encoding="utf-8" ?>', '<html><body>', '</body></html>'], '', $modifiedContent));

    libxml_clear_errors();

    return $modifiedContent;
}
add_filter('the_content', 'add_fancybox_to_gallery_block');

/**
 * Preload hero-home block background images only when the block is present
 */
function blast_preload_hero_home_images() {
    if (has_block('acf/hero-home')) {
        ?>
        <link rel="preload" as="image" href="<?php echo esc_url(THEME_URI . '/blocks/hero-home/img/hero-home-bg-desktop.webp'); ?>" media="(min-width: 768px)">
        <link rel="preload" as="image" href="<?php echo esc_url(THEME_URI . '/blocks/hero-home/img/home-hero-mobile-bg.webp'); ?>" media="(max-width: 767px)">
        <?php
    }
}
add_action('wp_head', 'blast_preload_hero_home_images');
