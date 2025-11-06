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

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Custom query args
$query_args = array(
    'post_type' => $post_type,
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'orderby' => $order_by,
    'order' => $order,
    'post_status' => 'publish'
);

// Create block ID
$block_id = 'archive-query-' . $block['id'];

// Build CSS classes
$classes = 'archive-query-block';
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

// Custom query
$custom_query = new WP_Query($query_args);

// If no posts and not in preview mode, show a message
if (!$custom_query->have_posts() && !$is_preview) {
    echo '<div class="archive-query-block no-posts"><p>' . __('No posts found.', 'blast') . '</p></div>';
    return;
}

// Use example data for preview if no posts
if (!$custom_query->have_posts() && $is_preview) {
    // Create fake posts for preview
    $fake_posts = array();
    for ($i = 1; $i <= min(6, $posts_per_page); $i++) {
        $fake_post = new stdClass();
        $fake_post->ID = $i;
        $fake_post->post_title = "Example Post Title $i";
        $fake_post->post_excerpt = "This is an example excerpt for post $i. It shows how your content will look when displayed in the grid layout.";
        $fake_post->post_date = date('Y-m-d H:i:s', strtotime("-$i days"));
        $fake_post->post_author = 1;
        $fake_post->post_type = $post_type;
        $fake_posts[] = $fake_post;
    }
}
?>

<div id="<?php echo esc_attr($block_id); ?>" <?php echo $wrapper_attributes; ?>>
    <div class="archive-query-inner">
        <div class="archive-query-container container">
            
            <?php if ($section_title) : ?>
                <div class="archive-query-header">
                    <h2 class="archive-query-title"><?php echo esc_html($section_title); ?></h2>
                </div>
            <?php endif; ?>
            
            <div class="archive-query-grid">
                <?php 
                if ($custom_query->have_posts()) :
                    while ($custom_query->have_posts()) : $custom_query->the_post();
                        $post_id = get_the_ID();
                        $post_url = get_permalink();
                        $post_title = get_the_title();
                        $post_excerpt = get_the_excerpt();
                        $post_date = get_the_date();
                        $post_author = get_the_author();
                        
                        // Truncate excerpt if needed
                        if ($show_excerpt && strlen($post_excerpt) > $excerpt_length) {
                            $post_excerpt = substr($post_excerpt, 0, $excerpt_length) . '...';
                        }
                ?>
                        <article class="archive-query-item post-item" data-post-id="<?php echo esc_attr($post_id); ?>">
                            
                            <?php if ($show_featured_image && has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php echo esc_url($post_url); ?>" aria-label="<?php echo esc_attr($post_title); ?>">
                                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                
                                <?php if ($show_categories && $post_type === 'post') : 
                                    $categories = get_the_category();
                                    if (!empty($categories)) :
                                ?>
                                    <div class="post-categories">
                                        <?php foreach ($categories as $category) : ?>
                                            <span class="post-category"><?php echo esc_html($category->name); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; endif; ?>
                                
                                <h3 class="post-title">
                                    <a href="<?php echo esc_url($post_url); ?>"><?php echo esc_html($post_title); ?></a>
                                </h3>
                                
                                <div class="post-meta">
                                    <?php if ($show_date) : ?>
                                        <span class="post-date">
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html($post_date); ?></time>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_author) : ?>
                                        <span class="post-author">
                                            <?php printf(__('by %s', 'blast'), esc_html($post_author)); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($show_excerpt && $post_excerpt) : ?>
                                    <div class="post-excerpt">
                                        <p><?php echo esc_html($post_excerpt); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($show_read_more) : ?>
                                    <div class="post-read-more">
                                        <a href="<?php echo esc_url($post_url); ?>" class="btn btn-primary read-more-link">
                                            <?php echo esc_html($read_more_text); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        </article>
                <?php 
                    endwhile;
                elseif ($is_preview && !empty($fake_posts)) :
                    foreach ($fake_posts as $fake_post) :
                ?>
                        <article class="archive-query-item post-item preview-item" data-post-id="<?php echo esc_attr($fake_post->ID); ?>">
                            
                            <?php if ($show_featured_image) : ?>
                                <div class="post-thumbnail">
                                    <div class="placeholder-image">
                                        <span>Featured Image</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                
                                <?php if ($show_categories && $post_type === 'post') : ?>
                                    <div class="post-categories">
                                        <span class="post-category">Example Category</span>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="post-title">
                                    <a href="#"><?php echo esc_html($fake_post->post_title); ?></a>
                                </h3>
                                
                                <div class="post-meta">
                                    <?php if ($show_date) : ?>
                                        <span class="post-date">
                                            <time><?php echo esc_html(date('F j, Y', strtotime($fake_post->post_date))); ?></time>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_author) : ?>
                                        <span class="post-author">
                                            <?php _e('by Author Name', 'blast'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($show_excerpt) : ?>
                                    <div class="post-excerpt">
                                        <p><?php echo esc_html(substr($fake_post->post_excerpt, 0, $excerpt_length)); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($show_read_more) : ?>
                                    <div class="post-read-more">
                                        <a href="#" class="btn btn-primary read-more-link">
                                            <?php echo esc_html($read_more_text); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        </article>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
            
            <?php if ($show_pagination && $custom_query->have_posts() && $custom_query->max_num_pages > 1) : ?>

         


                <div class="archive-query-pagination pagination">
                    <?php
                    $pagination_args = array(
                        'total' => $custom_query->max_num_pages,
                        'current' => $paged,
                        'format' => '?paged=%#%',
                        'show_all' => false,
                        'end_size' => 2,
                        'mid_size' => 1,
                        'prev_next' => true,
                        'prev_text' => __('&laquo; Previous', 'blast'),
                        'next_text' => __('Next &raquo;', 'blast'),
                        'type' => 'plain',
                        'add_args' => false,
                        'add_fragment' => '',
                        'before_page_number' => '',
                        'after_page_number' => ''
                    );
                    
                    echo paginate_links($pagination_args);
                    ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
// Reset post data
wp_reset_postdata();
?>