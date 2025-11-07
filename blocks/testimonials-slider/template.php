<?php
/**
 * Testimonials Slider Block Template
 *
 * A responsive testimonials slider with company logos and quotes using Splide.js
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

wp_enqueue_script('splide-slider');
wp_enqueue_style('splide-slider');

// Create id attribute allowing for custom "anchor" value.
$id = 'testimonials-slider-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'testimonials-slider';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get field values
$section_heading = get_field('section_heading') ?: 'Trusted by teams who build securely - at scale.';
$testimonials = get_field('testimonials') ?: [];
$slider_settings = get_field('slider_settings') ?: [];

// Slider settings with defaults
$autoplay = isset($slider_settings['autoplay']) ? $slider_settings['autoplay'] : true;
$autoplay_interval = isset($slider_settings['autoplay_interval']) ? $slider_settings['autoplay_interval'] : 5;
$slides_per_view = isset($slider_settings['slides_per_view']) ? $slider_settings['slides_per_view'] : 3;

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

<section <?php echo $wrapper_attributes; ?>>
    
    <div class="container container--xl">
        
        <!-- Section Heading -->
        <div class="testimonials-slider__header">
            <h2 class="testimonials-slider__heading">
                <?php echo wp_kses_post(highlight_words($section_heading, 'securely')); ?>
            </h2>
        </div>
    </div>
    <div class="testimonials-slider__container">
        <!-- Testimonials Slider -->
        <?php if (!empty($testimonials)): ?>
            <div class="testimonials-slider__wrapper">
                <div class="splide testimonials-slider__splide" 
                     data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
                     data-interval="<?php echo esc_attr($autoplay_interval * 1000); ?>"
                     data-slides-per-view="<?php echo esc_attr($slides_per_view); ?>">
                    
                    <div class="splide__track">
                        <ul class="splide__list">
                            
                            <?php foreach ($testimonials as $testimonial): ?>
                                <li class="splide__slide">
                                    <div class="testimonials-slider__card">
                                        
                                        <div class="testimonials-slider__card-content">
                                            <!-- Quote -->
                                            <div class="testimonials-slider__quote">
                                                <blockquote>
                                                    "<?php 
                                                    $quote_text = $testimonial['quote'];
                                                    // Limit quote to 340 characters
                                                    if (strlen($quote_text) > 321) {
                                                        $quote_text = substr($quote_text, 0, 321);
                                                        // Find the last complete word to avoid cutting mid-word
                                                        $last_space = strrpos($quote_text, ' ');
                                                        if ($last_space !== false && $last_space > 321) {
                                                            $quote_text = substr($quote_text, 0, $last_space);
                                                        }
                                                        $quote_text .= '...';
                                                    }
                                                    
                                                    echo wp_kses_post(highlight_words(
                                                        esc_html($quote_text), 
                                                        $testimonial['highlight_words'] ?? ''
                                                    )); 
                                                    ?>"
                                                </blockquote>

                                                <?php if (!empty($testimonial['author']) || !empty($testimonial['title']) || !empty($testimonial['company'])): ?>
                                                    <div class="testimonials-slider__author mobile-only">
                                                        <?php if (!empty($testimonial['image'])): ?>
                                                            <div class="testimonials-slider__author-image">
                                                                <img src="<?php echo esc_url($testimonial['image']['sizes']['medium'] ?: $testimonial['image']['url']); ?>" 
                                                                    alt="<?php echo esc_attr($testimonial['author']); ?> image"
                                                                    loading="lazy">
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($testimonial['author'])): ?>
                                                            <div class="testimonials-slider__author-name">
                                                                <?php echo $testimonial['author']; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                            
                                            
                                            <div class="testimonials-slider__info">
                                                <!-- Author Info -->
                                                <?php if (!empty($testimonial['author']) || !empty($testimonial['title']) || !empty($testimonial['company'])): ?>
                                                    <div class="testimonials-slider__author desktop-only">
                                                        <?php if (!empty($testimonial['image'])): ?>
                                                            <div class="testimonials-slider__author-image">
                                                                <img src="<?php echo esc_url($testimonial['image']['sizes']['medium'] ?: $testimonial['image']['url']); ?>" 
                                                                    alt="<?php echo esc_attr($testimonial['author']); ?> image"
                                                                    loading="lazy">
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($testimonial['author'])): ?>
                                                            <div class="testimonials-slider__author-name">
                                                                <?php echo $testimonial['author']; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Company Logo -->
                                                <?php if (!empty($testimonial['logo'])): ?>
                                                    <div class="testimonials-slider__logo">
                                                        <img src="<?php echo esc_url($testimonial['logo']['sizes']['medium'] ?: $testimonial['logo']['url']); ?>" 
                                                            alt="<?php echo esc_attr($testimonial['logo']['alt']); ?> logo"
                                                            loading="lazy">
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                        <img src="<?php echo THEME_URI ?>/img/testimonials-bg-effect.svg" alt="testimonials-bg-effect" loading="lazy" class="testimonials-slider__bg-effect">
                                        
                                    </div>
                                    <div class="testimonials-slider__bottom-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="49" height="38" viewBox="0 0 49 38" fill="none">
                                            <path d="M3.04371 36.3111C1.67776 37.069 0 36.0813 0 34.5191V26.5171V2.04938C0 0.917537 0.917537 0 2.04938 0H19.6649C20.7967 0 21.7143 0.917537 21.7143 2.04938V24.7446C21.7143 25.4893 21.3104 26.1753 20.6592 26.5366L3.04371 36.3111Z" fill="#000B40"/>
                                            <path d="M30.1783 20.8563C28.8125 21.6082 27.1406 20.6201 27.1406 19.061V11.0344V2.04938C27.1406 0.917538 28.0582 0 29.19 0H46.8055C47.9374 0 48.8549 0.917537 48.8549 2.04938V9.36344C48.8549 10.1105 48.4484 10.7985 47.7939 11.1587L30.1783 20.8563Z" fill="#000B40"/>
                                        </svg>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            
                        </ul>
                    </div>
                                        
                </div>
            </div>
        <?php else: ?>
            <div class="testimonials-slider__placeholder">
                <p>No testimonials added yet. Please add testimonials in the block settings.</p>
            </div>
        <?php endif; ?>
        
    </div>
    
</section>

<?php endif; ?>