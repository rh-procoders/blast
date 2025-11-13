<?php
/**
 * Features Section Block Template
 *
 * A features section with gradient background, heading, and feature cards with icons
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$block_name = 'cta-newsletter';
$id         = $block_name . '-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = $block_name;
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// ACF Fields.
$fields    = get_field( $block_name );
$heading   = $fields["{$block_name}__heading"] ?? null;
$cf7_id    = $fields["{$block_name}__contact-id"] ?? null;
$is_footer = $fields["{$block_name}__is-footer"] ?? FALSE;

// Add gradient class to main classes
$classes .=  $is_footer ? ' ' . $block_name . '--is-footer' : '';

$wrapper_attributes = get_block_wrapper_attributes( [
        'class' => $classes,
        'id'    => $id
] );
?>

<?php if ( isset( $block['data']['preview_image_help'] ) ): ?>
    <?php
    $fileUrl = str_replace( get_stylesheet_directory(), '', dirname( __FILE__ ) );
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else: ?>

    <section <?php echo $wrapper_attributes; ?>>
        <div class="<?= $block_name ?>__wrapper">
            <div class="<?= $block_name ?>__content">
                <?php
                if ( $heading ) : ?>
                    <span class="<?= $block_name ?>__heading">
                        <?= wp_kses_post( $heading ); ?>
                    </span>
                <?php
                endif; ?>

                <?php
                if ( $cf7_id ) : ?>
                    <div class="<?= $block_name ?>__cf7-wrapper">
                        <?= do_shortcode( '[contact-form-7 id="' . $cf7_id . '"]' ); ?>
                    </div>
                <?php
                endif; ?>
            </div>
        </div>
    </section>

<?php endif; ?>
