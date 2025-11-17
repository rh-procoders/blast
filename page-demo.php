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

// ACF Fields - Thank You Content
$fields_submit_success     = get_field( 'tpl-demo-submit_success' );
$submit_success_heading    = $fields_submit_success["tpl-demo-submit_success__heading"] ?? null;
$submit_success_subheading = $fields_submit_success["tpl-demo-submit_success__subheading"] ?? null;

?>

<figure class="demo__bg">
    <?php
    the_post_thumbnail( 'full', [ 'class' => '', 'title' => get_the_title() ] ); ?>
</figure>

<section class="demo-section">

    <!-- Close Button -->
    <button type="button" class="demo-section__close" data-demo-back aria-label="Close">
        <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
              <path d="M21.4141 1.41406L1.41406 21.4141" fill="none" stroke-width="2" stroke-linecap="square"/>
              <path d="M21.4141 21.4141L1.41406 1.41406" fill="none" stroke-width="2" stroke-linecap="square"/>
            </svg>
        </span>
    </button>

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
                <div class="clip-path">
                    <?php if ( $testimonial_quote ) : ?>
                        <blockquote class="demo-section__testimonial-quote">
                            <?= wp_kses_post( wpautop( $testimonial_quote ) ); ?>
                        </blockquote>
                    <?php endif; ?>

                    <?php if ( $testimonial_image || $testimonial_quoter ) : ?>
                        <div class="demo-section__testimonial-quoter">
                            <?php if ( $testimonial_image ) : ?>
                                <figure class="demo-section__testimonial-quoter-image">
                                    <img src="<?= esc_url( $testimonial_image['sizes']['large'] ?: $testimonial_image['url'] ); ?>"
                                         alt="<?= esc_attr( $testimonial_image['alt'] ?: 'Testimonial author' ); ?>"
                                         loading="lazy">
                                </figure>
                            <?php endif; ?>

                            <?php if ( $testimonial_quoter ) : ?>
                                <div class="demo-section__testimonial-quoter-name">
                                    <?= wp_kses_post( $testimonial_quoter ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php
                    endif; ?>

                    <?php if ( $testimonial_logo ) : ?>
                        <figure class="demo-section__testimonial-logo">
                            <img src="<?= esc_url( $testimonial_logo['sizes']['large'] ?: $testimonial_logo['url'] ); ?>"
                                 alt="<?= esc_attr( $testimonial_logo['alt'] ?: 'Company logo' ); ?>"
                                 loading="lazy">
                        </figure>
                    <?php endif; ?>
                </div>

                <svg class="demo-section__testimonial-artwork"
                     xmlns="http://www.w3.org/2000/svg"
                     width="49" height="38" viewBox="0 0 49 38" fill="none">
                    <path d="M3.04371 36.3111C1.67776 37.069 0 36.0813 0 34.5191V26.5171V2.04938C0 0.917537 0.917537 0 2.04938 0H19.6649C20.7967 0 21.7143 0.917537 21.7143 2.04938V24.7446C21.7143 25.4893 21.3104 26.1753 20.6592 26.5366L3.04371 36.3111Z"
                          fill="#000B40"/>
                    <path d="M30.1783 20.8563C28.8125 21.6082 27.1406 20.6201 27.1406 19.061V11.0344V2.04938C27.1406 0.917538 28.0582 0 29.19 0H46.8055C47.9374 0 48.8549 0.917537 48.8549 2.04938V9.36344C48.8549 10.1105 48.4484 10.7985 47.7939 11.1587L30.1783 20.8563Z"
                          fill="#000B40"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success Section (Hidden by default, shown after form submission) -->
    <div class="demo-section__success">
        <?php if ( $submit_success_heading ) : ?>
            <h2 class="demo-section__success-heading">
                <?= wp_kses_post( $submit_success_heading ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( $submit_success_subheading ) : ?>
            <p class="demo-section__success-subheading">
                <?= wp_kses_post( $submit_success_subheading ); ?>
            </p>
        <?php endif; ?>

        <div class="demo-section__success-socials">
            <span class="demo-section__success-socials_label">
                <?= __( "Meanwhile visit us on", 'blast-2025' ) ?>
            </span>

            <?php
            $social_links = get_field( 'social_media_links', 'option' );

            if ( $social_links ): ?>

                <div class="demo-section__success-socials_links">
                    <?php
                    foreach ($social_links as $social) :
                        $social_link = $social['link'] ?? null;
                        $social_icon = $social['icon'] ?? null;
                        ?>
                        <a href="<?= esc_url( $social_link ? $social_link['url'] : '#' ) ?>"
                           class="success_social_btn">
                            <!-- social icon here -->
                            <img class="success_social_btn-icon"
                                    src="<?php echo esc_url( $social_icon ? $social_icon['url'] : '#' ); ?>"
                                    alt="<?php echo esc_attr( $social_icon ? $social_icon['alt'] : 'alt' ); ?>"/>

                            <span class="success_social_btn-text"
                                  data-hover-text="<?= esc_html( $social_link ? $social_link['title'] : 'follow' ) ?>">
                                <?= esc_html( $social_link ? $social_link['title'] : 'follow' ) ?>
                            </span>

                            <span class="success_social_btn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                  <rect width="17.998" height="17.998" rx="6.88123" fill="#000B40"/>
                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8035 8.75H5.82422H11.8035Z" fill="white"/>
                                  <path d="M11.8035 8.75H5.82422" stroke="white" stroke-width="0.8" stroke-linecap="square"/>
                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3086 10.6185L12.1771 8.75L10.3086 10.6185Z" fill="white"/>
                                  <path d="M10.3086 10.6185L12.1771 8.75" stroke="white" stroke-width="0.8" stroke-linecap="square"/>
                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3086 6.88281L12.1771 8.75132L10.3086 6.88281Z" fill="white"/>
                                  <path d="M10.3086 6.88281L12.1771 8.75132" stroke="white" stroke-width="0.8" stroke-linecap="square"/>
                                </svg>
                            </span>
                        </a>
                    <?php
                    endforeach; ?>
                </div>

            <?php
            endif; ?>
        </div>
    </div>

</section>

<?php get_footer( 'demo' ); ?>
