<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package blast-2025
 */

?>


<footer id="footer" class="main-footer">


   
    <div class="container container--xl">

        <div class="main-footer__wrapper">
            <div class="footer-logo">
                <?php $footer_logo = get_field( 'footer_logo', 'option' ); ?>
                <?php if ( $footer_logo ) : ?>
                    <a href="<?php echo home_url() ?>">
                        <img src="<?php echo esc_url( $footer_logo['url'] ); ?>" alt="<?php echo esc_attr( $footer_logo['alt'] ); ?>" />
                    </a>
                <?php endif; ?>
            </div>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'footer-menu',
                    'menu_id'        => 'footer-menu',
                    'menu_class'    => 'footer-menu',
                    'container'     => 'nav',

                )
            );
            ?>
        </div>

        </div>
    </div>

    <?php $footer_image_placeholder = get_field( 'footer_image_placeholder', 'option' ); ?>
    <?php if ( $footer_image_placeholder ) : ?>
        <img src="<?php echo esc_url( $footer_image_placeholder['url'] ); ?>" alt="<?php echo esc_attr( $footer_image_placeholder['alt'] ); ?>" class="footer-image-placeholder"/>
    <?php endif; ?>

</footer>


</main><!-- #main -->

<?php wp_footer(); ?>

</body>
</html>
