<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package blast-2025
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">


    <link rel="apple-touch-icon" sizes="57x57" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href=" <?php echo THEME_URI ?>/img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href=" <?php echo THEME_URI ?>/img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href=" <?php echo THEME_URI ?>/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href=" <?php echo THEME_URI ?>/img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href=" <?php echo THEME_URI ?>/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo THEME_URI ?>/img/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo THEME_URI ?>/img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">


    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/Tomorrow/Tomorrow-BoldItalic.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/NeuePlak/NeuePlakRegular/font.woff2" as="font" type="font/woff2" crossorigin="anonymous">


    <script>
        let base_url = '<?php echo home_url(); ?>';
        let ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    </script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>
<header id="masthead" class="site-header">
    <div class="container container--xl">
        <div class="content-wrap">
            <div class="site-logo">
                <?php
                    if ( function_exists( 'the_custom_logo' ) ) {
                        the_custom_logo();
                    } else {
                        ?>
                        <a href="<?php echo home_url() ?>">
                        <h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
                        </a>
                        <?php
                    }
                    ?>
            </div>

            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" id="menu_toggle_button">
                <span class="toggle-text">Menu</span>
                <div class="toggle-icon">
                    <span class="toggle-icon-bar"></span>
                    <span class="toggle-icon-bar"></span>
                    <span class="toggle-icon-bar"></span>
                </div>
                <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'blast-2025' ); ?></span>
            </button>

            <nav id="site-navigation" class="site-navigation">
		        <?php
		        wp_nav_menu(
			        array(
				        'theme_location' => 'header-menu-desktop',
				        'menu_id'        => 'header-menu-desktop'
			        )
		        );
		        ?>
                <?php if ( have_rows( 'navigation_buttons', 'option' ) ) : ?>
                    <div class="navigation-buttons">
                    <?php while ( have_rows( 'navigation_buttons', 'option' ) ) : the_row(); ?>
                        <?php
                        $link = get_sub_field( 'link' );
                        $button_style = get_sub_field( 'button_style' );
                        if($button_style == 'primary_dark'){
                            $button_class = 'has-dark-blue-background-color has-background wp-element-button has-arrow-icon';
                        } elseif($button_style == 'secondary_bright'){
                            $button_class = 'has-coral-solid-gradient-background has-background wp-element-button has-arrow-icon';
                        } else {
                            $button_class = 'wp-element-button has-arrow-icon tertiary-button';
                        }
                        ?>
                        <?php if ( $link ) : ?>
                            <a class="wp-block-button__link btn <?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>">
                                <span class="button-text" data-hover-text="<?php echo esc_html( $link['title'] ); ?>"><?php echo esc_html( $link['title'] ); ?></span>
                                <span class="button-arrow-wrapper"><svg class="svg-icon icon-arrow-right" width="14" height="10" viewBox="0 0 14 10" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0441 4.59009H0.75H12.0441Z"></path>
                                        <path d="M12.0441 4.59009H0.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.21875 8.1195L12.7482 4.59009L9.21875 8.1195Z"></path>
                                        <path d="M9.21875 8.1195L12.7482 4.59009" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.21875 1.06067L12.7482 4.59008L9.21875 1.06067Z"></path>
                                        <path d="M9.21875 1.06067L12.7482 4.59008" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"></path>
                                    </svg>
                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </nav><!-- #site-navigation -->
        </div>
    </div>
</header><!-- #masthead -->

<main class="main">
