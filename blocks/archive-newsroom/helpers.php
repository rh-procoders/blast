<?php
/**
 * Archive Newsroom Block - AJAX Handler
 * Handles infinite scroll loading for newsroom posts
 *
 * @package blast-2025
 */

/**
 * AJAX Handler: Load archive newsroom posts for infinite scroll
 */
function blast_load_archive_newsroom_posts_callback(): void
{
    check_ajax_referer( 'blast_nonce', 'nonce', false );

    $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'newsroom';
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 6;
    $order_by = isset( $_POST['order_by'] ) ? sanitize_key( $_POST['order_by'] ) : 'date';
    $order = isset( $_POST['order'] ) ? sanitize_key( $_POST['order'] ) : 'DESC';
    $featured_ids = isset( $_POST['featured_ids'] ) ? json_decode( sanitize_text_field( $_POST['featured_ids'] ), true ) : [];

    $args = [
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'orderby'        => $order_by,
            'order'          => $order,
            'posts_per_page' => $posts_per_page,
            'paged'          => $page,
    ];

    // Exclude featured posts if provided
    if ( ! empty( $featured_ids ) && is_array( $featured_ids ) ) {
        $args['post__not_in'] = array_map( 'intval', $featured_ids );
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ):
        while ($query->have_posts()):
            $query->the_post();
            get_template_part( 'template-parts/components/newsroom-item' );
        endwhile;
    endif;

    wp_reset_postdata();

    $html = ob_get_clean();

    $response = [
            'success'    => true,
            'html'       => $html,
            'more_posts' => $query->found_posts > ( $page * $posts_per_page ),
    ];

    wp_send_json( $response );
}

add_action( 'wp_ajax_blast_load_archive_newsroom_posts', 'blast_load_archive_newsroom_posts_callback' );
add_action( 'wp_ajax_nopriv_blast_load_archive_newsroom_posts', 'blast_load_archive_newsroom_posts_callback' );
