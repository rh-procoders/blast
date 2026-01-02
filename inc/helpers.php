<?php

function cc_mime_types($mimes) {
	$mimes['json'] = 'application/json';
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');


function sprite_svg( $spriteName, $svgWidth = '24', $svgHeight = '24', $return = '' ) {
	$svg = get_stylesheet_directory_uri() . '/img/general/icon-sprites.svg?ver='. filemtime(get_template_directory() . '/img/general/icon-sprites.svg') .'#' . $spriteName;
	$elWidth = '';
	$elHeight = '';
	if (isset($svgWidth)) {
		$elWidth = 'width="' . $svgWidth . '"';
	}
	if (isset($svgHeight)) {
		$elHeight = 'height="' . $svgHeight . '"';
	}
	$iconHtml = '<svg class="svg-icon '. $spriteName .'" '.$elWidth.' '.$elHeight.'><use xlink:href="' . $svg . '"></use></svg>';
	if ($return) {
		return $iconHtml;
	} else {
		echo $iconHtml;
	}
}

if ( ! function_exists( 'return_sprite_svg' ) ) :

    /**
     *  returns SVG Icon from set sprite
     *
     *  Can dynamically set sprite source used when called
     *  sprite source has to be uploaded to /assets/images/icons/ folder
     *
     * @param string $sprite_name sprite icon name.
     * @param int    $svg_width sprite icon width.
     * @param int    $svg_height sprite icon height.
     * @param string $sprite_source sprite source file.
     *
     * @return string
     *
     * @throws Exception Throws error if sprite image directory is incorrect.
     */
    function return_sprite_svg(
        string $sprite_name,
        int $svg_width = 24,
        int $svg_height = 24,
        string $sprite_source = '/img/icons/icons.svg'
    ): string {

        // Detect if $sprite_source contains '/images/'.
        if ( str_contains( $sprite_source, '/img/' ) ) {
            // Get the substring after '/images/'.
            $sprite_source = substr( $sprite_source, strpos( $sprite_source, '/img/icons/' ) );
        } else {
            throw new Exception( 'Sprite Source Dir Incorrect! Upload to /img/icons/' );
        }

        $svg = get_stylesheet_directory_uri() . '/' . $sprite_source . '?ver=' . filemtime( get_template_directory() . '/' . $sprite_source ) . '#' . $sprite_name;

        $icon_html = '<svg class="svg-icon ' . $sprite_name . '" width="' . $svg_width . '" height="' . $svg_height . '"><use xlink:href="' . $svg . '"></use></svg>';

        // Define allowed attributes for SVG.
        $allowed_html = array(
            'svg' => array(
                'class'  => true,
                'width'  => true,
                'height' => true,
            ),
            'use' => array(
                'xlink:href' => true,
            ),
        );

        // Sanitize the SVG HTML output using wp_kses with the allowed attributes.
        return wp_kses( $icon_html, $allowed_html );
    }
endif;



// Plugin ACF Svg icon field
add_filter( 'acf/fields/svg_icon/file_path', 'tc_acf_svg_icon_file_path' );
function tc_acf_svg_icon_file_path( $file_path ) {
	return get_theme_file_path( '/img/general/icons.svg' );
}


/**
 * Custom navigation
 */
if ( ! function_exists( 'cf_pagination' ) ) {

	function cf_pagination( $args = array(), $class = 'pagination' ) {

		if ( $GLOBALS['wp_query']->max_num_pages <= 1 ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'mid_size'           => 2,
				'prev_next'          => true,
				'prev_text'          => __( '&laquo;', 'blast-2025' ),
				'next_text'          => __( '&raquo;', 'blast-2025' ),
				'screen_reader_text' => __( 'Posts navigation', 'blast-2025' ),
				'type'               => 'array',
				'current'            => max( 1, get_query_var( 'paged' ) ),
			)
		);

		$links = paginate_links( $args );

		?>

		<nav class="d-flex mt-2 mb-16" aria-label="<?php echo $args['screen_reader_text']; ?>">

			<ul class="pagination mx-auto">

				<?php
				foreach ( $links as $key => $link ) {
					?>
					<li class="page-item <?php echo strpos( strtolower($link), 'current' ) ? 'active' : ''; ?>">
						<?php echo str_replace( 'page-numbers', 'page-link', strtolower($link) ); ?>
					</li>
					<?php
				}
				?>

			</ul>

		</nav>

		<?php
	}
}


// function register_custom_query_vars( $qvars ) {
// 	$qvars[] = 'custom_query_var';
// 	return $qvars;
// }
// add_filter( 'query_vars', 'register_custom_query_vars' );


// slugify strings function
if ( ! function_exists( 'slugify' ) ) {
	function slugify($text, string $divider = '-')
	{
	  // replace non letter or digits by divider
	  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);

	  // trim
	  $text = trim($text, $divider);

	  // remove duplicate divider
	  $text = preg_replace('~-+~', $divider, $text);

	  // lowercase
	  $text = strtolower($text);

	  if (empty($text)) {
	    return 'n-a';
	  }

	  return $text;
	}
}



//add_filter( 'wpcf7_autop_or_not', '__return_false' );


/**
 * Add arrow icons to Gutenberg buttons
 */
function add_arrows_to_gutenberg_buttons($content) {
    // Only modify on frontend and if content is not empty
    if (is_admin() || empty(trim($content))) {
        return $content;
    }

    // Check if content contains button blocks
    if (strpos($content, 'wp-block-button') === false) {
        return $content;
    }

    // Use DOMDocument for reliable HTML parsing
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Find all button links
    $xpath = new DOMXPath($dom);
    $buttonLinks = $xpath->query('//div[contains(@class, "wp-block-button")]//a[contains(@class, "wp-block-button__link")]');

    foreach ($buttonLinks as $buttonLink) {
        // Add arrow icon class
        $currentClass = $buttonLink->getAttribute('class');
        $buttonLink->setAttribute('class', $currentClass . ' has-arrow-icon');

        // Get current button text
        $buttonText = $buttonLink->textContent;


        // Create simple arrow SVG (inline)
        $arrowSvg = '<svg class="svg-icon icon-arrow-right" width="14" height="10" viewBox="0 0 14 10" fill="currentColor">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0441 4.59009H0.75H12.0441Z"/>
            <path d="M12.0441 4.59009H0.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.21875 8.1195L12.7482 4.59009L9.21875 8.1195Z"/>
            <path d="M9.21875 8.1195L12.7482 4.59009" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.21875 1.06067L12.7482 4.59008L9.21875 1.06067Z"/>
            <path d="M9.21875 1.06067L12.7482 4.59008" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
        </svg>';


        // Clear the button content and add text + arrow
        $buttonLink->nodeValue = '';

        // Create text wrapper span
        $textWrapper = $dom->createElement('span');
        $textWrapper->setAttribute('class', 'button-text');
        $textWrapper->setAttribute('data-hover-text', $buttonText); // Add hover text data attribute
        $textNode = $dom->createTextNode($buttonText);
        $textWrapper->appendChild($textNode);
        $buttonLink->appendChild($textWrapper);

        // Create arrow wrapper
        $arrowWrapper = $dom->createElement('span');
        $arrowWrapper->setAttribute('class', 'button-arrow-wrapper');

        // Add the SVG to arrow wrapper
        $arrowFragment = $dom->createDocumentFragment();
        $arrowFragment->appendXML($arrowSvg);
        $arrowWrapper->appendChild($arrowFragment);

        $buttonLink->appendChild($arrowWrapper);
    }

    // Get the modified HTML
    $modifiedContent = $dom->saveHTML();

    // Remove the XML declaration that DOMDocument adds
    $modifiedContent = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<?xml encoding="utf-8" ?>', '<html><body>', '</body></html>'], '', $modifiedContent));

    libxml_clear_errors();

    return $modifiedContent;
}
// Hook to post/page content
add_filter('the_content', 'add_arrows_to_gutenberg_buttons', 20);

// Hook to widget content (for Gutenberg blocks in widgets)
add_filter('widget_block_content', 'add_arrows_to_gutenberg_buttons', 20);

// Hook to text widget content (if using classic text widgets with HTML)
add_filter('widget_text', 'add_arrows_to_gutenberg_buttons', 20);

function highlight_words($text, $words_to_highlight) {
    if (empty($words_to_highlight)) {
        return $text;
    }

    $words = array_map('trim', explode(',', $words_to_highlight));
    foreach ($words as $word) {
        if (!empty($word)) {
            $text = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', '<strong>$0</strong>', $text);
        }
    }
    return $text;
}


/**
 * Auto-load all shortcodes from the shortcodes folder
 */
function blast_load_shortcodes() {
    $shortcodes_dir = get_template_directory() . '/shortcodes/';

    if (!is_dir($shortcodes_dir)) {
        return;
    }

    $shortcode_files = glob($shortcodes_dir . '*.php');

    foreach ($shortcode_files as $file) {
        require_once $file;
    }
}
blast_load_shortcodes();





if ( ! function_exists( 'dd' ) ) {
    /**
     * Custom Beautify var_dump() / Kill Operation
     *  Loads if symfony/var-dumper not found
     *
     * @param mixed  $data          Data to be dumped.
     * @param string $label         Label for the dumped content - by default it's the file name.
     * @param bool   $should_return Define if output should be returned or echoed out.
     * @param bool   $kill          Define if operation should be killed.
     *
     * @return string|void
     *
     * src: https://www.learn-codes.net/php/php-colors-in-var-dump-ubuntu/
     */
    function dd( mixed $data, string $label = '', bool $should_return = false, bool $kill = true ) {
        $debug             = debug_backtrace();
        $calling_file      = $debug[0]['file'];
        $calling_file_line = $debug[0]['line'];

        ob_start();
        var_dump( $data );
        $c = ob_get_contents();
        ob_end_clean();

        $c = preg_replace( "/\r\n|\r/", "\n", $c );
        $c = str_replace( "]=>\n", '] = ', $c );
        $c = preg_replace( '/= {2,}/', '= ', $c );
        $c = preg_replace( '/\["(.*?)"] = /i', '[$1] = ', $c );
        $c = preg_replace( '/ {2}/', '    ', $c );
        $c = preg_replace( '/""(.*?)"/i', '"$1"', $c );
        $c = preg_replace( '/(int|float)\(([0-9.]+)\)/i', '$1() <span class="number">$2</span>', $c );

        // Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
        $c = preg_replace( '/(\[[\w ]+] = string\([0-9]+\) )"(.*?)/sim', '$1<span class="string">"', $c );
        $c = preg_replace( "/(\"\n+)( *})/im", '$1</span>$2', $c );
        $c = preg_replace( "/(\"\n+)( *\[)/im", '$1</span>$2', $c );
        $c = preg_replace( "/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c );

        $regex = array(
            // Numbers.
            'numbers'  => array(
                    '/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i',
                    '$1$2(<span class="number">$3</span>)',
            ),
            // Keywords.
            'null'     => array( '/(^|] = )(null)/i', '$1<span class="keyword">$2</span>' ),
            'bool'     => array( '/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)' ),
            // Types.
            'types'    => array( '/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)' ),
            // Objects.
            'object'   => array( '/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)' ),
            // Function.
            'function' => array(
                    '/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i',
                    '$1<span class="function">$2</span>(',
            ),
        );

        foreach ( $regex as $x ) {
            $c = preg_replace( $x[0], $x[1], $c );
        }

        $style = '
        /* outside div - it will float and match the screen */
        .dumper {
            margin: 2px;
            padding: 2px;
            background-color: #fbfbfb;
            float: left;
            clear: both;
            box-sizing: unset;
            white-space: unset;
        }
        /* font size and family */
        .dumper pre {
            color: #000000;
            font-size: 9pt;
            font-family: "Courier New",Courier,Monaco,monospace;
            margin: 0px;
            padding-top: 5px;
            padding-bottom: 7px;
            padding-left: 9px;
            padding-right: 9px;
            box-sizing: unset;
            white-space: pre;
        }
        /* inside div */
        .dumper div {
            background-color: #fcfcfc;
            border: 1px solid #d9d9d9;
            float: left;
            clear: both;
            box-sizing: unset;
            white-space: pre;
        }
        /* syntax highlighting */
        .dumper span.string {color: #c40000;}
        .dumper span.number {color: #ff0000;}
        .dumper span.keyword {color: #007200;}
        .dumper span.function {color: #0000c4;}
        .dumper span.object {color: #ac00ac;}
        .dumper span.type {color: #0072c4;}
        ';

        $style = preg_replace( '/ {2,}/', '', $style );
        $style = preg_replace( "/\t|\r\n|\r|\n/", '', $style );
        $style = preg_replace( '/\/\*.*?\*\//i', '', $style );
        $style = str_replace( '}', '} ', $style );
        $style = str_replace( ' {', '{', $style );
        $style = trim( $style );

        $c = trim( $c );
        $c = preg_replace( "/\n<\/span>/", "</span>\n", $c );

        if ( '' === $label ) {
            $line1 = '';
        } else {
            $line1 = "<strong>$label</strong> \n";
        }

        $out = "\n<!-- dumper Begin -->\n" .
                '<style>' . $style . "</style>\n" .
                "<div class=\"dumper\">
                <div><pre>$line1 $calling_file : $calling_file_line \n$c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>" .
                "\n<!-- dumper End -->\n";
        if ( $should_return ) {
            return $out;
        } else {
            echo $out;
        }

        if ( $kill ) {
            die();
        }
    }
}

/**
 * Get singular form of taxonomy name for URL parameter
 *
 * Converts taxonomy names to user-friendly, SEO-friendly URL parameter names.
 * Example: 'event_types' becomes 'event_type', 'post_tag' becomes 'tag'
 *
 * @param string $taxonomy Taxonomy name
 * @return string Singular form for URL parameter
 */
function blast_get_taxonomy_param_name( string $taxonomy ): string
{
    // Map plural taxonomy names to singular URL-friendly parameter names
    $param_map = [
        'category'    => 'category',
        'post_tag'    => 'tag',
        'event_types' => 'event_type',
    ];

    // Return mapped name or remove trailing 's' as fallback
    if ( isset( $param_map[ $taxonomy ] ) ) {
        return $param_map[ $taxonomy ];
    }

    // Fallback: remove trailing 's' if exists
    return rtrim( $taxonomy, 's' );
}
