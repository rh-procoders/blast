<?php
/**
 * FAQ Block
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'faq-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'faq-section';
$class_align = '';
if ( ! empty( $block['align'] ) ) {
    $class_align .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

$heading = get_field('heading') ?: 'FAQ';
$faq_items = get_field('faq_items') ?: [];

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);

?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="faq-container <?php echo esc_attr($class_align); ?>">
        <!-- Heading -->
        <?php if (!empty($heading)) : ?>
            <h2 class="faq-heading"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>

        <!-- FAQ Accordion -->
        <div class="faq-accordion">
            <?php if (!empty($faq_items)) : ?>
                <?php foreach ($faq_items as $index => $item) : ?>
                    <div class="faq-item" data-index="<?php echo esc_attr($index); ?>">
                        <button class="faq-item-trigger" aria-expanded="false" aria-controls="faq-content-<?php echo esc_attr($index); ?>">
                            <span class="faq-question"><?php echo esc_html($item['question']); ?></span>
                            <span class="faq-toggle-icon">
                                <span class="faq-icon-line horizontal"></span>
                                <span class="faq-icon-line vertical"></span>
                            </span>
                        </button>
                        <div class="faq-item-content" id="faq-content-<?php echo esc_attr($index); ?>" hidden>
                            <div class="faq-answer">
                                <?php echo wp_kses_post($item['answer']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
