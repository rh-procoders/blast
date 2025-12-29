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
$block_name = 'speakers';
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
$items = get_field( $block_name );


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
        <div class="<?= $block_name ?>__content">

            <?php
            foreach ($items as $key => $item) :
                $image = $item["speaker__image"] ?? null;
                $name = $item["speaker__name"] ?? null;
                $position = $item["speaker__position"] ?? FALSE;
                ?>
                <div class="<?= $block_name ?>__item">
                    <?php
                    if ( $image ) : ?>
                        <img class="<?= $block_name ?>__avatar"
                             src="<?php echo esc_url( $image['url'] ); ?>"
                             alt="<?php echo esc_attr( $image['title'] ); ?>"
                             loading="lazy">
                    <?php
                    endif; ?>

                    <div class="<?= $block_name ?>__person">
                        <?php
                        if ( $name ) : ?>
                            <span class="<?= $block_name ?>__person-name">
                                <?= wp_kses_post( $name ) ?>
                            </span>
                        <?php
                        endif; ?>

                        <?php
                        if ( $position ) : ?>
                            <span class="<?= $block_name ?>__person-position">
                                <?= wp_kses_post( $position ) ?>
                            </span>
                        <?php
                        endif; ?>
                    </div>
                </div>
            <?php
            endforeach; ?>
        </div>
    </section>

<?php endif; ?>
