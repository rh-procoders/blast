<?php
/**
 * Default WordPress entry point for the page
 * set as "Posts Page" from Settings > Reading.
 *
 * @package blast-2025
 */

get_header();

// Get the page set as "Posts Page" for title/content
$page_for_posts = get_option('page_for_posts');

if ($page_for_posts):
    $page = get_post($page_for_posts);

    // Check if title should be hidden
    $hide_title = get_field('hide_title', $page_for_posts);

    if (!$hide_title): ?>
        <h1 class="page-title"><?= esc_html($page->post_title); ?></h1>
    <?php endif;

    // Optionally display page content
    // echo apply_filters('the_content', $page->post_content);
endif;

// Render blog filter shortcode ONCE
echo do_shortcode('[blast-blog-filter]');

get_footer();
