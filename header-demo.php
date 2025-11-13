<?php
/**
 * Demo Page Header
 * Minimal header for demo page template
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

<main class="main">
