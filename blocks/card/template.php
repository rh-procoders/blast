<?php
/**
 * Card Block Template
 *
 * A reusable card component with icon, heading, description, and optional button
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'card-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'card-block';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$card_image = get_field('card_image');
$card_tag = get_field('card_tag') ?: '';
$card_heading = get_field('card_heading') ?: 'Card Heading';
$card_description = get_field('card_description') ?: '';
$card_link = get_field('card_link') ?: '';

$button_label = get_field('button_label') ?: null;
$author_image = get_field('author_image') ?: null;
$author_name = get_field('author_name') ?: null;

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);
?>

<?php if (isset( $block['data']['preview_image_help'] )  ): ?>
	<?php 
	$fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__), );
	echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	?>
<?php else: ?>


<div <?php echo $wrapper_attributes; ?>>

    
    <!-- Card Image -->
    <?php if ($card_image): ?>
        <div class="card-block__image">
            <img src="<?php echo esc_url($card_image['sizes']['large'] ?: $card_image['url']); ?>" 
                 alt="<?php echo esc_attr($card_image['alt'] ?: $card_heading); ?>"
                 loading="lazy">
        </div>
    <?php endif; ?>
    
    <!-- Card Content -->
    <div class="card-block__content">
        
        <!-- Card Tag -->
        <?php if ($card_tag): ?>
            <div class="card-block__tag-wrapper">
            <?php foreach ($card_tag as $tag): 
                $color = $tag['color'] ?: '#000B40';
                $bg_color = $tag['background_color'] ?: 'transparent';
                ?>
                <span class="card-block__tag" style="color: <?php echo esc_attr($color); ?>; background-color: <?php echo esc_attr($bg_color); ?>;">
                    <?php echo esc_html($tag['label']); ?>
                </span>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Card Heading -->
   
        <a href="<?php echo esc_url($card_link['url']); ?>" 
           <?php if ($card_link['target']): ?>target="<?php echo esc_attr($card_link['target']); ?>"<?php endif; ?>
           <?php if ($card_link['title']): ?>aria-label="<?php echo esc_attr($card_link['title']); ?>"<?php endif; ?>
           class="h4 card-block__heading">
            <?php echo $card_heading; ?>
        </a>
        
        <!-- Card Description -->
        <?php if ($card_description): ?>
            <p class="card-block__description">
                <?php echo esc_html($card_description); ?>
            </p>
        <?php endif; ?>
        <?php if(get_field('show_author') || $button_label): ?>
        <div class="card-block__footer">
            <?php if(get_field('show_author')): ?>
                <div class="card-block__author">
                    <?php if ($author_image): ?>
                        <div class="card-block__author-image">
                            <img 
                                src="<?php echo esc_url($author_image['url']); ?>" 
                                alt="<?php echo esc_attr($author_name); ?>"
                                loading="lazy"
                            >
                        </div>
                    <?php endif; ?>
                    <?php if ($author_name): ?>
                        <span class="card-block__author-name">
                            <?php echo $author_name; ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($button_label): ?>
                <div  class="card-block__button btn  btn-outline">
                    <span class="button-text"
                        data-hover-text="<?php echo esc_html($button_label); ?>">
                        <?php echo esc_html($button_label); ?>
                    </span>

                    <span class="button-arrow-wrapper">
                        <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
    </div>
    

</div>

<?php endif; ?>