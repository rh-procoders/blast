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
    $posts_per_page = isset($_POST['posts_per_page']) ? max(1, intval($_POST['posts_per_page'])) : 4;
    $post_type      = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';
    $term_slug      = isset($_POST['term']) ? sanitize_key($_POST['term']) : '';
    $search         = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $taxonomy       = isset($_POST['taxonomy']) ? sanitize_key($_POST['taxonomy']) : 'category';
    $is_archive     = isset($_POST['is_archive']) && $_POST['is_archive'] === 'true';
    $tax_id         = !empty($_POST['tax_id']) ? absint($_POST['tax_id']) : null;

    // Build query args
    $args = [
        'post_type'      => $post_type,
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

    // Exclude "Uncategorized" category (only for category taxonomy)
    // To disable this exclusion, comment out the lines below
    if ($taxonomy === 'category') {
        $uncategorized = get_category_by_slug('uncategorized');
        if ($uncategorized) {
            $args['category__not_in'] = [$uncategorized->term_id];
        }
    }

    // Handle taxonomy filtering
    if ($tax_id) {
        // Filter by specific taxonomy term (archive mode)
        if ($taxonomy === 'category') {
            $args['category__in'] = [$tax_id];
        } elseif ($taxonomy === 'tag') {
            $args['tag_id'] = $tax_id;
        } elseif ($taxonomy === 'author') {
            $args['author'] = $tax_id;
        } else {
            // General handling for any custom taxonomy
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $tax_id,
                ],
            ];
        }
    } elseif (!empty($term_slug) && $term_slug !== 'all') {
        // Filter by URL parameter (normal mode)
        if ($taxonomy === 'category') {
            $category = get_category_by_slug($term_slug);
            if ($category) {
                $args['category__in'] = [$category->term_id];
            }
        } elseif ($taxonomy === 'tag') {
            $tag = get_term_by('slug', $term_slug, 'post_tag');
            if ($tag) {
                $args['tag_id'] = $tag->term_id;
            }
        } else {
            // General handling for any custom taxonomy
            $term = get_term_by('slug', $term_slug, $taxonomy);
            if ($term) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $term->term_id,
                    ],
                ];
            }
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

            if ( $post_type === 'events' ) {
                get_template_part( 'template-parts/components/event-filter-item' );
            } else {
                get_template_part( 'template-parts/components/blog-filter-item' );
            }

            $response .= ob_get_clean();
        }

        // Check if more posts are available for the next page
        if ($query->found_posts > ($paged * $posts_per_page)) {
            $more_posts = true;
        }
    } else {
        // No posts found - display message
        $response = sprintf(
            '<div class="blog-filter__no-results"><p>%s</p></div>',
            esc_html__('No articles found...', 'blast-2025')
        );
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
