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

<section class="demo">
    <div class="left">
        <?php
        if ( $left_title ) : ?>
            <?= wp_kses_post( $left_title ); ?>
        <?php
        endif; ?>

        <br>

        <?php
        if ( $left_benefits ) : ?>
            <ul>
                <?php
                foreach ($left_benefits as $key => $benefit) :
                    $benefit_icon = $benefit["tpl-demo-left-benefit__icon"] ?? null;
                    $benefit_description = $benefit["tpl-demo-left-benefit__description"] ?? null; ?>
                    <li>
                        <?php
                        if ( $benefit_icon ) : ?>
                            <img src="<?php echo esc_url( $benefit_icon['sizes']['large'] ?: $benefit_icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $benefit_icon['alt'] ?: 'benefit_icon' ); ?>"
                                 loading="lazy">
                        <?php
                        endif; ?>

                        <?php
                        if ( $benefit_description ) : ?>
                            <?= wp_kses_post( $benefit_description ); ?>
                        <?php
                        endif; ?>
                    </li>
                <?php
                endforeach; ?>
            </ul>
        <?php
        endif; ?>

        <?php
        if ( $left_testimonial ) :
            $testimonial_quote = $left_testimonial["tpl-demo-left-testimonial__quote"] ?? null;
            $testimonial_image = $left_testimonial["tpl-demo-left-testimonial__image"] ?? null;
            $testimonial_quoter = $left_testimonial["tpl-demo-left-testimonial__quoter"] ?? null;
            $testimonial_logo = $left_testimonial["tpl-demo-left-testimonial__logo"] ?? null; ?>

            <?php
            if ( $testimonial_quote ) : ?>
                <?= wp_kses_post( wpautop( $testimonial_quote ) ); ?>
            <?php
            endif; ?>

            <br>

            <?php
            if ( $testimonial_image ) : ?>
                <img src="<?php echo esc_url( $testimonial_image['sizes']['large'] ?: $testimonial_image['url'] ); ?>"
                     alt="<?php echo esc_attr( $testimonial_image['alt'] ?: 'benefit_icon' ); ?>"
                     loading="lazy">
            <?php
            endif; ?>

            <br>

            <?php
            if ( $testimonial_quoter ) : ?>
                <?= wp_kses_post( $testimonial_quoter ); ?>
            <?php
            endif; ?>

            <br>

            <?php
            if ( $testimonial_logo ) : ?>
                <img src="<?php echo esc_url( $testimonial_logo['sizes']['large'] ?: $testimonial_logo['url'] ); ?>"
                     alt="<?php echo esc_attr( $testimonial_logo['alt'] ?: 'benefit_icon' ); ?>"
                     loading="lazy">
            <?php
            endif; ?>

        <?php
        endif; ?>
    </div>

    <div class="right">
        <?php
        if ( $right_label ) : ?>
            <?= wp_kses_post( $right_label ); ?>
        <?php
        endif; ?>

        <br>

        <?php
        if ( $right_title ) : ?>
            <?= wp_kses_post( $right_title ); ?>
        <?php
        endif; ?>

        <?php
        if ( $right_cf7_id ) : ?>
            <?= do_shortcode( '[contact-form-7 id="' . esc_attr( $right_cf7_id ) . '"]' ); ?>
        <?php
        endif; ?>
    </div>
</section>

<?php get_footer( 'demo' ); ?>
