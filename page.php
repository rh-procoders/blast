<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

get_header();

        while ( have_posts() ) :
        the_post();


        if (get_field('hide_title') == false): ?> 
        <h1 class="page-title"><?php the_title(); ?></h1>
        <?php endif; ?>

        <?php the_content();


        endwhile; // End of the loop.
        ?>


<?php
get_footer();
