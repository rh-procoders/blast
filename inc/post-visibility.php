<?php
declare(strict_types=1);

/**
 * Post Visibility
 *
 * Lets an editor mark a blog post as "Unlisted" (bs-post__is-unlisted ACF field).
 * Unlisted posts stay published and load fine at their permalink, but are
 * filtered out of every query that lists posts: the blog archive/search main
 * query, and any custom WP_Query built for post_type "post" (archive-hero,
 * archive-query block, blog-filter shortcode + its AJAX handler, related
 * posts, etc.), since none of those queries suppress filters.
 *
 * @package blast-2025
 */

add_action( 'pre_get_posts', 'blast_hide_unlisted_posts', 20 );
function blast_hide_unlisted_posts( WP_Query $query ): void
{
    // Let wp-admin list/edit screens keep showing unlisted posts; still filter
    // the public-facing AJAX requests, which also run under is_admin().
    if ( is_admin() && ! wp_doing_ajax() ) {
        return;
    }

    // Never touch the single-post query itself, that's the whole point of "unlisted".
    if ( $query->is_singular() || $query->is_preview() ) {
        return;
    }

    $post_types = (array) ( $query->get( 'post_type' ) ?: 'post' );

    if ( ! in_array( 'post', $post_types, true ) ) {
        return;
    }

    $unlisted_clause = [
        'relation' => 'OR',
        [
            'key'     => 'bs-post__is-unlisted',
            'compare' => 'NOT EXISTS',
        ],
        [
            'key'     => 'bs-post__is-unlisted',
            'value'   => '1',
            'compare' => '!=',
        ],
    ];

    $existing_meta_query = $query->get( 'meta_query' );

    if ( ! empty( $existing_meta_query ) ) {
        $query->set( 'meta_query', [
            'relation' => 'AND',
            $existing_meta_query,
            $unlisted_clause,
        ] );
    } else {
        $query->set( 'meta_query', $unlisted_clause );
    }
}
