<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package blast-2025
 */

?>


<footer id="footer" class="main-footer">

    <!-- Main Footer Content -->
    <div class="main-footer__wrapper">

        <div class="main-footer__grid">

            <!-- Footer Logo & Brand -->
            <div class="main-footer__brand">
                <div class="footer-logo">
                    <?php $footer_logo = get_field( 'footer_logo', 'option' ); ?>
                    <?php if ( $footer_logo ) : ?>
                        <a href="<?php echo home_url() ?>">
                            <img src="<?php echo esc_url( $footer_logo['url'] ); ?>" alt="<?php echo esc_attr( $footer_logo['alt'] ); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer Navigation Columns -->
            <div class="main-footer__navigation">

                <!-- Company Column -->
                <div class="footer-column">
                    <?php
                    $company_links = get_field('company_links', 'option');
                    if ($company_links): ?>
                    <h4 class="footer-column__title">
                        <?php echo get_field('company_column_title', 'option') ?: 'Company'; ?>
                    </h4>
                        <ul class="footer-column__links">
                            <?php foreach ($company_links as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link']['url']); ?>"
                                        <?php echo $link['link']['target'] ? 'target="' . $link['link']['target'] . '"' : ''; ?>>
                                        <?php echo esc_html($link['link']['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Platform Column -->
                <div class="footer-column">
                    <?php
                    $platform_links = get_field('platform_links', 'option');
                    if ($platform_links): ?>
                    <h4 class="footer-column__title">
                        <?php echo get_field('platform_column_title', 'option') ?: 'Platform'; ?>
                    </h4>
                        <ul class="footer-column__links">
                            <?php foreach ($platform_links as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link']['url']); ?>"
                                        <?php echo $link['link']['target'] ? 'target="' . $link['link']['target'] . '"' : ''; ?>>
                                        <?php echo esc_html($link['link']['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Resources Column -->
                <div class="footer-column">
                    <?php
                    $resources_links = get_field('resources_links', 'option');
                    if ($resources_links): ?>
                    <h4 class="footer-column__title">
                        <?php echo get_field('resources_column_title', 'option') ?: 'Resources'; ?>
                    </h4>
                        <ul class="footer-column__links">
                            <?php foreach ($resources_links as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link']['url']); ?>"
                                        <?php echo $link['link']['target'] ? 'target="' . $link['link']['target'] . '"' : ''; ?>>
                                        <?php echo esc_html($link['link']['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Newsletter Signup -->
            <div class="main-footer__newsletter">
                <div class="footer-newsletter">
                    <h4 class="footer-newsletter__title">
                        <?php echo get_field('newsletter_title', 'option') ?: 'Stay Updated'; ?>
                    </h4>
                    <p class="footer-newsletter__description">
                        <?php echo get_field('newsletter_description', 'option') ?: 'Get Updates on Preemptive Cloud Defense'; ?>
                    </p>
                    <?php if (get_field('newsletter_form', 'option')): ?>
                    <!-- Newsletter Form -->
                    <?php echo do_shortcode(get_field('newsletter_form', 'option')); ?>
                    <?php endif; ?>

                </div>
                <!-- Social Media Icons -->
                <?php $social_links = get_field('social_media_links', 'option'); ?>
                <?php if ($social_links): ?>
                    <div class="footer-social">
                        <?php foreach ($social_links as $social):
                            $social_link = $social['link'] ?? null;
                            $social_icon = $social['icon'] ?? null;
                            ?>
                            <a href="<?php echo esc_url($social_link ? $social_link['url'] : '#'); ?>"
                                class="footer-social__link"
                                target="<?= esc_attr($social_link ? $social_link['target'] : '_blank'); ?>"
                                rel="noopener noreferrer">
                                <img src="<?php echo esc_url($social_icon ? $social_icon['url'] : '#'); ?>" alt="<?php echo esc_attr($social_icon ? $social_icon['alt'] : 'alt'); ?>" />
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php $footer_badges = get_field('footer_badges', 'option'); ?>
                <?php  if ($footer_badges): ?>
                    <div class="footer-badges">
                        <?php foreach ($footer_badges as $badge):
                            $badge_image = $badge['badge_image'] ?? null;
                            $badge_link = $badge['badge_link'] ?? null;
                            ?>
                            <a href="<?php echo esc_url($badge_link ? $badge_link['url'] : '#'); ?>"
                                class="footer-badges__link"
                                target="_blank"
                                rel="noopener noreferrer">
                                <img src="<?php echo esc_url($badge_image ? $badge_image['url'] : '#'); ?>" alt="<?php echo esc_attr($badge_image ? $badge_image['alt'] : 'alt'); ?>" />
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Footer Bottom -->
        <div class="main-footer__bottom">
            <div class="main-footer__bottom-content">
                <div class="footer-copyright">
                    <p><?php echo get_field('copyright_text', 'option') ?: '© ' . date('Y') . ' Blast Security. All rights reserved.'; ?></p>
                </div>
                <div class="footer-legal">
                    <?php
                    $legal_links = get_field('legal_links', 'option');
                    if ($legal_links): ?>
                        <ul class="footer-legal__links">
                            <?php foreach ($legal_links as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link']['url']); ?>">
                                        <?php echo esc_html($link['link']['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Background Elements -->
        <img src="<?php echo THEME_URI ?>/img/footer-bg-graph.png" alt="footer graphic" class="main-footer__bg-image-bottom" loading="lazy"/>

    </div>

    <?php $footer_image_placeholder = get_field( 'footer_image_placeholder', 'option' ); ?>
    <?php if ( $footer_image_placeholder ) : ?>
        <?php $footer_lottie = get_field( 'footer_lottie_file', 'option' ); ?>
        <?php if ( $footer_lottie ) : ?>
            <lottie-player src="<?php echo esc_url( $footer_lottie['url'] ); ?>" background="transparent" speed="1" loop autoplay></lottie-player>
        <?php else : ?>
            <img src="<?php echo esc_url( $footer_image_placeholder['url'] ); ?>" alt="<?php echo esc_attr( $footer_image_placeholder['alt'] ); ?>" class="footer-image-placeholder"/>
        <?php endif; ?>
    <?php endif; ?>

</footer>

</main><!-- #main -->

<?php wp_footer(); ?>

</body>
</html>
