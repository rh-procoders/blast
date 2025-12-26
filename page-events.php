<?php
/**
 * Template for Events page (/events/)
 *
 * Content managed through Gutenberg blocks/shortcodes
 *
 * @package blast-2025
 */

wp_enqueue_style('blast-archive');

get_header();



// Display the current page's content
while (have_posts()): the_post();
    the_content();
endwhile;

get_footer();
