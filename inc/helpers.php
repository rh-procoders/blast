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
	$iconHtml = '<svg class="svg-icon '. $spriteName .'" '.$elWidth.' '.$elHeight.'"><use xlink:href="' . $svg . '"></use></svg>';
	if ($return) {
		return $iconHtml;
	} else {
		echo $iconHtml;
	}
}


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
add_filter('the_content', 'add_arrows_to_gutenberg_buttons', 20);

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


