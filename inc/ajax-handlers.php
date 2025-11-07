<?php
declare(strict_types=1);

/**
 * AJAX Handlers for Blast Theme
 *
 * @package blast-2025
 */

/**
 * AJAX Callback: Blog Filter
 * Handles filtering and pagination for blog posts
 *
 * Security Notes:
 * - No nonce verification needed (public read-only endpoint, no user login)
 * - Uses referrer check to prevent external requests
 * - All inputs sanitized to prevent injection
 * - WP Engine + Cloudflare provide infrastructure-level DDoS protection
 *
 * @return void
 */
function blast_filter_posts(): void
{
    // Security: Basic referrer check (prevents external requests)
    // This checks that the request is coming from the same site
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], home_url()) !== 0) {
        wp_send_json_error('Invalid request origin');
        wp_die();
    }

    // Validate and sanitize inputs
    $paged          = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $posts_per_page = 4; // Fixed: always 4 posts
    $category_slug  = isset($_POST['category']) ? sanitize_key($_POST['category']) : '';
    $search         = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Build query args
    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    ];

    // Add search query if provided
    if (!empty($search)) {
        $args['s'] = $search;
    }

    // Exclude "Uncategorized" category
    // To disable this exclusion, comment out the lines below
    $uncategorized = get_category_by_slug('uncategorized');
    if ($uncategorized) {
        $args['category__not_in'] = [$uncategorized->term_id];
    }

    // Add category filter if specific category is selected
    if (!empty($category_slug) && $category_slug !== 'all') {
        $category = get_category_by_slug($category_slug);
        if ($category) {
            $args['category__in'] = [$category->term_id];
        }
    }

    // Run the query
    $query      = new WP_Query($args);
    $response   = '';
    $more_posts = false;

    // Display Posts
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            ob_start();
            get_template_part('template-parts/components/blog-filter-item');
            $response .= ob_get_clean();
        }

        // Check if more posts are available for the next page
        if ($query->found_posts > ($paged * $posts_per_page)) {
            $more_posts = true;
        }
    }

    wp_reset_postdata();

    // Return response in JSON
    wp_send_json([
        'html'       => $response,
        'more_posts' => $more_posts,
    ]);

    wp_die();
}

// Register AJAX actions for both logged-in and non-logged-in users
add_action('wp_ajax_blast_filter_posts', 'blast_filter_posts');
add_action('wp_ajax_nopriv_blast_filter_posts', 'blast_filter_posts');