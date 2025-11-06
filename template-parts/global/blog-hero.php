<div class="container">
    <div class="breadcrumbs">
        <ul class="breadcrumbs-list">
            <li>
                <a href="<?php echo home_url('/'); ?>">Home</a>
            </li>
            <li><span class="separator">|</span></li>
            <li>
                <span class="title"><?php echo esc_html( get_the_title() ); ?></span>
            </li>
        </ul>
    </div>
    <div class="hero-page__content">
        <h1 class="hero-page__title"><?php echo esc_html( get_the_title() ); ?></h1>

        <?php get_template_part( 'template-parts/global/search-bar' ); ?>        
    </div>
</div>      