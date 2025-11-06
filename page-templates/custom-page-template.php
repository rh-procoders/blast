<?php
/*
Template name: Custom Page Template
 */
get_header();
    if ( have_posts() ) :
        /* Start the Loop */
        while ( have_posts() ) :
            the_post();
            ?>

            <?php
			the_content();
			?>

            
            
        <?php endwhile;

    endif; ?>

<?php get_footer(); ?>