<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */


$post_id = get_the_ID();
$categories = get_the_category();
?>

<article id="post-<?php the_ID(); ?>" class="article-item">
    <div class="article-item__header">
        <a href="<?php the_permalink(); ?>" class="article-image">
            <?php the_post_thumbnail( 'blog-thumbnails' ); ?>
        </a>
    </div>
    <div class="article-item__body">
        <div class="categories">
            <?php
            if ( ! empty( $categories ) ) {
                echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
            }
            ?>
        </div>
        <h4 class="h4">
            <a href="<?php the_permalink(); ?>">
               <?php the_title(); ?> 
            </a>
        </h4>
    </div>
    <div class="article-item__footer">
        <div class="author-meta">
            <div class="author-meta__avatar">
                <?php
                echo get_avatar( get_the_author_meta( 'ID' ));
                ?>
            </div>
            <div class="author-meta__name">
                <?php blast_theme_wp_posted_by(); ?>
            </div>
        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->

