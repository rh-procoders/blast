<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package blast-2025
 */

// Get TOC from Global
global $single_toc;

?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-content">

        <div class="entry-content__sidebar-left">
            <div class="sticky-sidebar">
                <?php
                if ( $single_toc ) : ?>
                    <div class="bs-toc">
                        <div class="bs-toc__container">
                            <?php
                            echo $single_toc; ?>
                        </div>
                    </div>
                <?php
                endif; ?>
            </div>
        </div><!-- .entry-content__sidebar-left -->

        <div class="entry-content__the-content">

            <!-- Back to Blog -->
            <a class="entry-content__backlink"
               href="<?= get_post_type_archive_link( 'post' ) ?>">
                <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>

                <?= __( "Back to Blog", 'blast-2025' ) ?>
            </a>

            <div class="entry-content__wrapper">

                <?php
                the_title( '<h1 class="entry-content__title">', '</h1>' );
                ?>

                <div class="entry-content__excerpt">
                    <?php the_excerpt(); ?>
                </div>

                <div class="entry-content__meta">
                    <?php
                    get_template_part( 'template-parts/components/author', null, [
                            'user_id'     => get_the_author_meta( 'ID' ),
                            'avatar_size' => 50,
                            'inline'      => true
                    ] );
                    ?>

                    <span class="post-date">
                        <?= esc_html( get_the_date( 'jS M, Y' ) ) ?>
                    </span>
                </div>

                <figure class="entry-content__thumbnail">
                    <?php
                    the_post_thumbnail( 'full', [ 'class' => '', 'title' => get_the_title() ] );
                    ?>
                </figure>

                <?php
                the_content(
                        sprintf(
                                wp_kses(
                                /* translators: %s: Name of current post. Only visible to screen readers */
                                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'blast-2025' ),
                                        array(
                                                'span' => array(
                                                        'class' => array(),
                                                ),
                                        )
                                ),
                                wp_kses_post( get_the_title() )
                        )
                );

                ?>
            </div>
        </div><!-- .entry-content__the-content -->

        <!-- Widget & Socials -->
        <div class="entry-content__sidebar-right">
            <div class="sticky-sidebar">
                <?php
                // Display post content sidebar widget if active
                if ( is_active_sidebar( 'post-content-sidebar' ) ) :
                    dynamic_sidebar( 'post-content-sidebar' );
                endif;

                // Social share buttons
                get_template_part( 'template-parts/components/post-social-share' );
                ?>
            </div>
        </div><!-- .entry-content__sidebar-right -->
    </div><!-- .entry-content -->

    <div class="entry-author">
        <div class="entry-author__wrapper">
            <?php
            get_template_part( 'template-parts/components/author', null, [
                    'user_id'           => get_the_author_meta( 'ID' ),
                    'avatar_size'       => 150,
                    'inline'            => true,
                    'show_job_position' => true,
                    'show_bio'          => true
            ] );
            ?>
        </div>
    </div>

    <?php
    // Get current post's categories
    $current_categories = wp_get_post_categories( get_the_ID() );

    // Only show related posts if current post has categories
    if ( ! empty( $current_categories ) ) :
        // Query for related posts
        $related_query = new WP_Query( [
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 2,
                'post__not_in'        => [ get_the_ID() ], // Exclude current post
                'category__in'        => $current_categories,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
        ] );

        // Only display section if we have related posts
        if ( $related_query->have_posts() ) : ?>
            <div class="entry-related">
                <div class="entry-related__wrapper">
                    <!-- Section Heading -->
                    <h2 class="entry-related__heading">
                        <?php
                        /* translators: "like" should be emphasized/styled */
                        echo wp_kses_post( __( 'You might also <strong>like</strong>', 'blast-2025' ) );
                        ?>
                    </h2>

                    <!-- Related Posts Grid -->
                    <div class="entry-related__grid">
                        <?php
                        while ($related_query->have_posts()) :
                            $related_query->the_post();
                            get_template_part( 'template-parts/components/related-post-item' );
                        endwhile;
                        wp_reset_postdata(); // Restore global post data
                        ?>
                    </div>
                </div>
            </div>
        <?php
        endif;
    endif;
    ?>

    <script id="single-post-scripts" type="text/javascript">
        /* ==========================================================================
          EZ‑TOC  ↔  2‑level (H2 + H3) highlighter
          ========================================================================== */
        (function () {
            const TOP_OFFSET = 150; // px

            /* ------------------------------------------------------------------ 1 */
            const tocList =
                document.querySelector( '.bs-toc .ez-toc-list' )
                || document.querySelector( '.the_sidebar > .the_sidebar__toc .ez-toc-list' );

            if (!tocList) {
                return;
            }

            /* ------------------------------------------------------------------ 2 */
            const idToLi = Object.create( null );
            tocList.querySelectorAll( 'li > a.ez-toc-link' ).forEach( a => {
                idToLi[a.hash.slice( 1 )] = a.parentElement; // id → LI
            } );

            const firstH2Li = tocList.querySelector( 'li.ez-toc-heading-level-2' );
            if (!firstH2Li) {
                return;
            }

            /* ------------------------------------------------------------------ 3 */
            const spans = Array.from( document.querySelectorAll( 'span.ez-toc-section[id]' ) )
                .filter( span => idToLi[span.id] );

            if (!spans.length) {
                return;
            }

            /* ------------------------------------------------------------------ 4 */
            let lastActiveH2 = null;
            let lastActiveH3 = null;

            /* Store indices for direction tracking */
            const allLiElements = Array.from( tocList.querySelectorAll( 'li' ) );

            const setActive = ( li, ref ) => {
                if (ref.current === li) {
                    return;
                }

                const oldLi = ref.current;
                const newLi = li;

                // Determine scroll direction based on DOM position
                if (oldLi && newLi) {
                    const oldIndex = allLiElements.indexOf( oldLi );
                    const newIndex = allLiElements.indexOf( newLi );
                    const scrollingDown = newIndex > oldIndex;

                    // Add exit animation to old item
                    if (scrollingDown) {
                        oldLi.classList.add( 'exiting-to-bottom' );
                    } else {
                        oldLi.classList.add( 'exiting-to-top' );
                    }

                    // Add enter animation to new item
                    if (scrollingDown) {
                        newLi?.classList.add( 'entering-from-top' );
                    } else {
                        newLi?.classList.add( 'entering-from-bottom' );
                    }

                    // Clean up old item classes after animation
                    setTimeout( () => {
                        oldLi.classList.remove( 'active', 'exiting-to-bottom', 'exiting-to-top' );
                    }, 500 );

                    // Clean up new item animation classes after animation
                    setTimeout( () => {
                        newLi?.classList.remove( 'entering-from-top', 'entering-from-bottom' );
                    }, 500 );
                } else {
                    // First time activation (no animation)
                    ref.current?.classList.remove( 'active' );
                }

                // Add active class to new item
                li?.classList.add( 'active' );
                ref.current = li;
            };

            /* object wrappers so we can pass by reference */
            const refH2 = {current: null};
            const refH3 = {current: null};

            /* ------------------------------------------------------------------ 5 */
            let ticking = false;
            const resolve = () => {
                ticking = false;
                let currentSectionLi = null; // H2 that wraps the viewport slice
                let currentLi = null; // last span whose top ≤ TOP_OFFSET

                for (const span of spans) {
                    if (span.getBoundingClientRect().top - TOP_OFFSET <= 0) {
                        const li = idToLi[span.id];
                        currentLi = li;
                        if (li.classList.contains( 'ez-toc-heading-level-2' )) {
                            currentSectionLi = li; // remember H2
                        }
                    } else {
                        break;
                    }
                }

                /* If we haven’t crossed any span yet, default to the first H2 */
                if (!currentSectionLi) {
                    currentSectionLi = firstH2Li;
                }

                /* === Apply highlights === */
                setActive( currentSectionLi, refH2 );

                if (currentLi &&
                    currentLi.classList.contains( 'ez-toc-heading-level-3' ) &&
                    currentLi !== refH3.current) {
                    setActive( currentLi, refH3 ); // highlight H3
                } else if (!currentLi ||
                    !currentLi.classList.contains( 'ez-toc-heading-level-3' )) {
                    setActive( null, refH3 ); // clear H3 when none
                }
            };

            const onScroll = () => {
                if (!ticking) {
                    ticking = true;
                    requestAnimationFrame( resolve );
                }
            };

            /* ------------------------------------------------------------------ 6 */
            resolve(); // highlight on load
            document.addEventListener( 'scroll', onScroll, {passive: true} );
            window.addEventListener( 'resize', onScroll, {passive: true} );

        })();
    </script>

</article><!-- #post-<?php the_ID(); ?> -->
