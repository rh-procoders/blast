<?php
/**
 * Template Name: Demo Page
 *
 * Demo request page template that looks like a modal
 * Full page with faded background and centered container
 *
 * @package blast-2025
 */

get_header( 'demo' );

// ACF Fields - Left Column
$fields_left      = get_field( 'tpl-demo-left' );
$left_title       = $fields_left["tpl-demo-left__title"] ?? null;
$left_benefits    = $fields_left["tpl-demo-left-benefits"] ?? null;
$left_testimonial = $fields_left["tpl-demo-left-testimonial"] ?? null;

// ACF Fields - Right Column
$fields_right = get_field( 'tpl-demo-right' );
$right_label  = $fields_right["tpl-demo-right__label"] ?? null;
$right_title  = $fields_right["tpl-demo-right__title"] ?? null;
$right_cf7_id = $fields_right["tpl-demo-right__cf7-id"] ?? null;

?>

<section class="demo-section">

    <!-- Form Section (First in HTML, visually on right) -->
    <div class="demo-section__form">
        <?php if ( $right_label ) : ?>
            <span class="demo-section__form-label">
                <?= wp_kses_post( $right_label ); ?>
            </span>
        <?php endif; ?>

        <?php if ( $right_title ) : ?>
            <h1 class="demo-section__form-title">
                <?= wp_kses_post( $right_title ); ?>
            </h1>
        <?php endif; ?>

        <?php if ( $right_cf7_id ) : ?>
            <div class="demo-section__form-content">
                <?= do_shortcode( '[contact-form-7 id="' . esc_attr( $right_cf7_id ) . '"]' ); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Content Section (Second in HTML, visually on left) -->
    <div class="demo-section__content">
        <?php if ( $left_title ) : ?>
            <h2 class="demo-section__content-title">
                <?= wp_kses_post( $left_title ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( $left_benefits ) : ?>
            <ul class="demo-section__benefits">
                <?php foreach ($left_benefits as $benefit) :
                    $benefit_icon = $benefit["tpl-demo-left-benefit__icon"] ?? null;
                    $benefit_description = $benefit["tpl-demo-left-benefit__description"] ?? null;
                    ?>
                    <li class="demo-section__benefit">
                        <?php if ( $benefit_icon ) : ?>
                            <figure class="demo-section__benefit-icon">
                                <img src="<?= esc_url( $benefit_icon['sizes']['large'] ?: $benefit_icon['url'] ); ?>"
                                     alt="<?= esc_attr( $benefit_icon['alt'] ?: 'Benefit icon' ); ?>"
                                     loading="lazy">
                            </figure>
                        <?php endif; ?>

                        <?php if ( $benefit_description ) : ?>
                            <div class="demo-section__benefit-description">
                                <?= wp_kses_post( $benefit_description ); ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php
        if ( $left_testimonial ) :
            $testimonial_quote = $left_testimonial["tpl-demo-left-testimonial__quote"] ?? null;
            $testimonial_image = $left_testimonial["tpl-demo-left-testimonial__image"] ?? null;
            $testimonial_quoter = $left_testimonial["tpl-demo-left-testimonial__quoter"] ?? null;
            $testimonial_logo = $left_testimonial["tpl-demo-left-testimonial__logo"] ?? null;
            ?>

            <div class="demo-section__testimonial">
                <?php if ( $testimonial_quote ) : ?>
                    <blockquote class="demo-section__testimonial-quote">
                        <?= wp_kses_post( wpautop( $testimonial_quote ) ); ?>
                    </blockquote>
                <?php endif; ?>

                <?php if ( $testimonial_image ) : ?>
                    <figure class="demo-section__testimonial-image">
                        <img src="<?= esc_url( $testimonial_image['sizes']['large'] ?: $testimonial_image['url'] ); ?>"
                             alt="<?= esc_attr( $testimonial_image['alt'] ?: 'Testimonial author' ); ?>"
                             loading="lazy">
                    </figure>
                <?php endif; ?>

                <?php if ( $testimonial_quoter ) : ?>
                    <div class="demo-section__testimonial-quoter">
                        <?= wp_kses_post( $testimonial_quoter ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $testimonial_logo ) : ?>
                    <figure class="demo-section__testimonial-logo">
                        <img src="<?= esc_url( $testimonial_logo['sizes']['large'] ?: $testimonial_logo['url'] ); ?>"
                             alt="<?= esc_attr( $testimonial_logo['alt'] ?: 'Company logo' ); ?>"
                             loading="lazy">
                    </figure>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

</section>

<?php get_footer( 'demo' ); ?>
