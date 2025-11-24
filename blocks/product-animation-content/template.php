<?php
/**
 * Product Animation Content Block
 */



$heading = get_field('heading') ?: 'Preemptive by Design. <strong>Resilient</strong> by Nature.';
$second_title = get_field('second_title') ?: '';
$description = get_field('description') ?: '';
$lottie_url = get_field('lottie_url') ?: '';
$bottom_image = get_field('bottom_image_file') ?: null;
$bottom_image_mobile = get_field('bottom_image_file_mobile') ?: null;
$content_items = get_field('content_items') ?: [];

// Prepare content items with positions
$positioned_items = [];
if (!empty($content_items)) {
    foreach ($content_items as $index => $item) {
        $positioned_items[] = [
            'label' => $item['label'] ?? '',
            'title' => $item['title'] ?? '',
            'content' => $item['content'] ?? '',
            'position' => $item['position'] ?? 'top-left',
            'index' => $index
        ];
    }
}
?>

<section class="pac-product-animation-content">
    <div class="pac-container">
        <div class="pac-header">
            <h2 class="pac-heading"><?php echo wp_kses_post($heading); ?></h2>
            <?php if (!empty($description)) : ?>
                <p class="pac-description"><?php echo wp_kses_post($description); ?></p>
            <?php endif; ?>
        </div>

        <div class="pac-content-wrapper">
            <!-- Lottie Animation Container -->
            <div class="pac-lottie-container">
                <?php if (!empty($lottie_url)) : ?>
                     <lottie-player 
                        id="lottie-desktop-<?php echo $block['id']; ?>"
                        class="pac-lottie"
                        src="<?php echo esc_url( $lottie_url ); ?>"  
                        background="transparent"  
                        speed="0.6"
                        loop="true"
                        autoplay
                        style="width: 100%; height: 100%;"  
                        >
                    </lottie-player>
                <?php else : ?>
                    <div class="pac-lottie-placeholder">
                        <p>Lottie Animation Placeholder</p>
                    </div>
                <?php endif; ?>

                <!-- Content Dots with Hover Information -->
                <div class="pac-dots-container">
                    <?php if (!empty($positioned_items)) : ?>
                        <?php foreach ($positioned_items as $item) : ?>
                            <div class="pac-content-dot" data-position="<?php echo esc_attr($item['position']); ?>" data-index="<?php echo esc_attr($item['index']); ?>">
                                <div class="pac-dot-button">
                                    <?php echo sprite_svg('icon-animation-plus', 14, 14); ?>
                                </div>
                                <span class="pac-dot-label"><?php echo esc_html($item['label']); ?></span>
                            </div>
                            <div class="pac-content-item" data-position="<?php echo esc_attr($item['position']); ?>" data-index="<?php echo esc_attr($item['index']); ?>">
                                
                                <div class="pac-dot-body">
                                    <?php echo wp_kses_post($item['content']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
            <div class="pac-content-mobile">
                <?php if (!empty($positioned_items)) : ?>
                    <?php foreach ($positioned_items as $item) : ?>
                        <div class="pac-content-item" data-position="<?php echo esc_attr($item['position']); ?>" data-index="<?php echo esc_attr($item['index']); ?>">
                            <div class="pac-dot-body">
                                <span class="pac-dot-label"><?php echo esc_html($item['label']); ?></span>
                                <p><?php echo wp_kses_post($item['content']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="pac-bottom">
            <h3 class="h2 pac-heading"><?php echo wp_kses_post($second_title); ?></h3>
            <div class="pac-bottom-image">
                <?php if ($bottom_image) : ?>
                    <img src="<?php echo esc_url($bottom_image['url']); ?>" alt="<?php echo esc_attr($bottom_image['alt']); ?>" loading="lazy" class="desktop-only pac-bottom-image-desktop">
                <?php endif; ?>
                
                <?php if ($bottom_image_mobile) : ?>
                    <img src="<?php echo esc_url($bottom_image_mobile['url']); ?>" alt="<?php echo esc_attr($bottom_image_mobile['alt']); ?>" loading="lazy" class="mobile-only pac-bottom-image-mobile">
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
