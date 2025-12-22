<?php
/**
 * Archive Query Block Template
 * 
 * @package blastTheme
 * @var array $block The block settings and attributes
 * @var string $content The block inner HTML (empty)
 * @var bool $is_preview True during AJAX preview
 * @var int $post_id The post ID this block is saved to
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$section_title = get_field('section_title');
$post_type = get_field('post_type') ?: 'post';
$posts_per_page = get_field('posts_per_page') ?: 6;
$grid_columns = get_field('grid_columns') ?: 3;
$show_pagination = get_field('show_pagination');
$show_featured_image = get_field('show_featured_image');
$show_excerpt = get_field('show_excerpt');
$excerpt_length = get_field('excerpt_length') ?: 150;
$show_date = get_field('show_date');
$show_author = get_field('show_author');
$show_categories = get_field('show_categories');
$show_read_more = get_field('show_read_more');
$read_more_text = get_field('read_more_text') ?: 'Read More';
$order_by = get_field('order_by') ?: 'date';
$order = get_field('order') ?: 'DESC';

$featured_posts = get_field('choose_featured_posts');

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Create block ID
$block_id = 'archive-newsroom-' . $block['id'];

// Build CSS classes
$classes = 'archive-newsroom-block';
$classes .= ' grid-cols-' . $grid_columns;
$classes .= ' post-type-' . $post_type;

// Add alignment class if set
if (!empty($block['align'])) {
    $classes .= ' align' . $block['align'];
}

// Add custom CSS classes if set
if (!empty($block['className'])) {
    $classes .= ' ' . $block['className'];
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $classes
]);


// Build initial query
$args = [
        'post_type'      => 'newsroom',
        'post_status'    => 'publish',
        'orderby'        => $order_by,
        'order'          => $order,
        'posts_per_page' => $posts_per_page,
        'paged'          => 1,
];

// Get featured posts to exclude from main query

if ($featured_posts && is_array($featured_posts)) {
    $featured_post_ids = array_map(function($post) {
        return $post->ID;
    }, $featured_posts);
    $args['post__not_in'] = $featured_post_ids;
}

// Run initial query
$initial_query  = new WP_Query( $args );

$has_more_posts = $initial_query->found_posts > $args['posts_per_page'];


// If no posts and not in preview mode, show a message
if (!$initial_query->have_posts() && !$is_preview) {
    echo '<div class="archive-query-block no-posts"><p>' . __('No posts found.', 'blast') . '</p></div>';
    return;
}

// Use example data for preview if no posts

?>

<div id="<?php echo esc_attr($block_id); ?>" <?php echo $wrapper_attributes; ?>>
    <div class="newsroom-archive">

        <!-- Posts Grid -->
        <div class="newsroom-archive__grid container">
            <?php

            // Display featured posts if they exist
            if ($featured_posts && is_array($featured_posts)) {
                echo '<div class="newsroom-archive__featured">';
                foreach ($featured_posts as $featured_post) {
                    $GLOBALS['post'] = $featured_post;
                    setup_postdata($featured_post);
                    get_template_part('template-parts/components/newsroom-item');
                }
                wp_reset_postdata();
                echo '</div>';
            }

            if ( $initial_query->have_posts() ): ?>
                <div class="archive-newsroom__list" id="append-newsroom-posts">
               <?php  while ($initial_query->have_posts()):
                    $initial_query->the_post();
                    get_template_part( 'template-parts/components/newsroom-item' );
                endwhile; ?>
                </div>
            <?php
            else:
                ?>
                <div class="newsroom-archive__no-results">
                    <p><?= esc_html__( 'No newsroom articles found...', 'blast-2025' ) ?></p>
                </div>
            <?php
            endif;

            wp_reset_postdata();
            ?>
        </div><!-- /.newsroom-archive__grid -->

        <!-- Load More Button (Hidden for infinite scroll) -->
        <div class="newsroom-archive__load-more wp-block-button is-style-outline is-style-outline--1" style="<?= ! $has_more_posts ? 'display: none;' : '' ?>">
            <button class="newsroom-archive__load-more-btn btn has-arrow-icon" data-page="2">
                <span class="button-text"
                      data-hover-text="<?= esc_attr__( 'Load More', 'blast-2025' ) ?>">
                    <?= esc_html__( 'Load More', 'blast-2025' ) ?>
                </span>

                <span class="button-arrow-wrapper">
                    <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>
                </span>
            </button>
        </div>

        <!-- Hidden data attributes for AJAX -->
        <div class="newsroom-archive__data" style="display: none;"
             data-post-type="newsroom"
             data-posts-per-page="<?= esc_attr($posts_per_page) ?>"
             data-order-by="<?= esc_attr($order_by) ?>"
             data-order="<?= esc_attr($order) ?>"
             data-featured-ids="<?= esc_attr(json_encode(array_map(function($p) { return $p->ID; }, $featured_posts ?: []))) ?>"
             data-has-more="<?= esc_attr($has_more_posts ? 'true' : 'false') ?>">
        </div>

    </div><!-- /.newsroom-archive -->

</div>

<?php
// Reset post data
wp_reset_postdata();
?>