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
<?php 
$choose_page_to_show_top_bar = get_field( 'choose_page_to_show_top_bar', 'option' );
$enable_top_bar = get_field( 'enable_top_bar', 'option' );
$current_page_id = get_the_ID();

if (  ( is_array( $choose_page_to_show_top_bar ) && in_array( $current_page_id, $choose_page_to_show_top_bar ) &&  $enable_top_bar == true)): ?>

<!-- Top Announcement Banner -->
<div class="top-banner">
    <div class="top-banner__container">
        <div class="top-banner__content">
            <div class="top-banner__text">
            <?php the_field( 'top_bar_content', 'option' ); ?>    
            </div>
        </div>
        <div class="top-banner__action">
            <?php $top_bar_link = get_field( 'top_bar_link', 'option' ); ?>
            <?php if ( $top_bar_link ) : ?>
                <a href="<?php echo esc_url( $top_bar_link['url'] ); ?>" target="<?php echo esc_attr( $top_bar_link['target'] ); ?>" class="top-banner__link">
                    <span><?php echo esc_html( $top_bar_link['title'] ); ?></span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="20" height="20" rx="7.64706" fill="#000B40"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.1123 9.72656H6.46875H13.1123Z" fill="white"/>
                        <path d="M13.1123 9.72656H6.46875" stroke="white" stroke-width="0.882353" stroke-linecap="square"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4531 11.8027L13.5292 9.72656L11.4531 11.8027Z" fill="white"/>
                        <path d="M11.4531 11.8027L13.5292 9.72656" stroke="white" stroke-width="0.882353" stroke-linecap="square"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4531 7.64844L13.5292 9.72456L11.4531 7.64844Z" fill="white"/>
                        <path d="M11.4531 7.64844L13.5292 9.72456" stroke="white" stroke-width="0.882353" stroke-linecap="square"/>
                    </svg>

                </a>
            <?php endif; ?>

        </div>
        <button class="top-banner__close" aria-label="Close banner">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
</div>

<?php endif; ?>

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
                <div class="navigation-buttons mobile-only">
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
            <?php if ( have_rows( 'navigation_buttons', 'option' ) ) : ?>
            <div class="navigation-buttons desktop-only">
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
        </div>
    </div>
</header><!-- #masthead -->

<main class="main">
