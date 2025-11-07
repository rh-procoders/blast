<?php
declare(strict_types=1);

/**
 * Blog Filter Shortcode
 * Displays filterable blog posts with category filter, search, and load more
 *
 * @package blast-2025
 */

/**
 * Shortcode: [blast-blog-filter]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function blast_blog_filter_shortcode( array $atts ): string
{
    // Parse shortcode attributes
    $atts = shortcode_atts( [
            'posts_per_page' => 4,
            'tax'            => 'category',  // Taxonomy type: category, tag, author
            'is_archive'     => 'false',     // Archive mode flag
            'tax_id'         => null,        // Specific taxonomy ID to filter
    ], $atts, 'blast-blog-filter' );

    // Get current category from URL parameter (if not in archive mode with tax_id)
    $current_category = isset( $_GET['category'] ) ? sanitize_key( $_GET['category'] ) : 'all';
    $current_search   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

    // If tax_id is provided, we're filtering by a specific term
    $taxonomy   = sanitize_key( $atts['tax'] );
    $is_archive = $atts['is_archive'] === 'true';
    $tax_id     = ! empty( $atts['tax_id'] ) ? absint( $atts['tax_id'] ) : null;

    // Get taxonomy terms based on taxonomy type (only if not filtering by tax_id)
    $terms = [];
    if ( ! $tax_id ) {
        if ( $taxonomy === 'category' ) {
            $terms = get_categories( [ 'hide_empty' => true ] );
            // Filter out Uncategorized category
            $terms = array_filter( $terms, function ( $term ) {
                return $term->slug !== 'uncategorized';
            } );
        } elseif ( $taxonomy === 'tag' ) {
            $terms = get_tags( [ 'hide_empty' => true ] );
        }
        // Note: Author taxonomy handled differently (no filter UI for authors)
    }

    ob_start();
    ?>

    <div class="blog-filter container container--blog-filter">

        <!-- Filter Bar -->
        <div class="blog-filter__bar"
             data-taxonomy="<?= esc_attr( $taxonomy ) ?>"
             data-is-archive="<?= esc_attr( $is_archive ? 'true' : 'false' ) ?>"
             data-tax-id="<?= esc_attr( $tax_id ?? '' ) ?>">

            <!-- Search Input -->
            <div class="blog-filter__search">
                <input type="text"
                       class="blog-filter__search-input"
                       id="blog-filter-search"
                       name="blog-filter-search"
                       placeholder="<?= esc_attr__( 'Search', 'blast-2025' ) ?>"
                       value="<?= esc_attr( $current_search ) ?>"/>
                <?php sprite_svg( 'icon-filter-search', 20, 20 ) ?>
            </div>

            <!-- Taxonomy Filter (Categories/Tags) -->
            <?php // Only show if not filtering by specific tax_id
            if ( ! $tax_id && ! empty( $terms ) ): ?>
                <div class="blog-filter__categories">
                <span class="blog-filter__category-label">
                    <?= esc_attr__( 'Filter', 'blast-2025' ) ?>
                </span>
                    <ul class="blog-filter__category-list">
                        <?php foreach ($terms as $term): ?>
                            <li>
                                <a href="?category=<?= esc_attr( $term->slug ) ?>"
                                   class="blog-filter__category-item <?= $current_category === $term->slug ? 'blog-filter__category-item--active' : '' ?>"
                                   data-category="<?= esc_attr( $term->slug ) ?>">
                                    <?= esc_html( $term->name ) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div><!-- /.blog-filter__bar -->

        <!-- Posts Grid -->
        <div class="blog-filter__grid container container--blog-grid">
            <?php
            // Build initial query based on URL parameters
            $args = [
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'posts_per_page' => intval( $atts['posts_per_page'] ),
                    'paged'          => 1,
            ];

            // Add search
            if ( ! empty( $current_search ) ) {
                $args['s'] = $current_search;
            }

            // Exclude Uncategorized (only for category taxonomy)
            if ( $taxonomy === 'category' ) {
                $uncategorized = get_category_by_slug( 'uncategorized' );
                if ( $uncategorized ) {
                    $args['category__not_in'] = [ $uncategorized->term_id ];
                }
            }

            // Handle taxonomy filtering
            if ( $tax_id ) {
                // Filter by specific taxonomy term (archive mode)
                if ( $taxonomy === 'category' ) {
                    $args['category__in'] = [ $tax_id ];
                } elseif ( $taxonomy === 'tag' ) {
                    $args['tag_id'] = $tax_id;
                } elseif ( $taxonomy === 'author' ) {
                    $args['author'] = $tax_id;
                }
            } elseif ( $current_category !== 'all' ) {
                // Filter by URL parameter (normal mode)
                if ( $taxonomy === 'category' ) {
                    $category = get_category_by_slug( $current_category );
                    if ( $category ) {
                        $args['category__in'] = [ $category->term_id ];
                    }
                } elseif ( $taxonomy === 'tag' ) {
                    $tag = get_term_by( 'slug', $current_category, 'post_tag' );
                    if ( $tag ) {
                        $args['tag_id'] = $tag->term_id;
                    }
                }
            }

            // Run initial query
            $initial_query  = new WP_Query( $args );
            $has_more_posts = $initial_query->found_posts > $args['posts_per_page'];

            if ( $initial_query->have_posts() ):
                while ($initial_query->have_posts()):
                    $initial_query->the_post();
                    get_template_part( 'template-parts/components/blog-filter-item' );
                endwhile;
            else:
                ?>
                <div class="blog-filter__no-results">
                    <p><?= esc_html__( 'No posts found.', 'blast-2025' ) ?></p>
                </div>
            <?php
            endif;

            wp_reset_postdata();
            ?>
        </div><!-- /.blog-filter__grid -->

        <!-- Load More Button -->
        <div class="blog-filter__load-more wp-block-button is-style-outline is-style-outline--1" style="<?= ! $has_more_posts ? 'display: none;' : '' ?>">
            <button class="blog-filter__load-more-btn btn has-arrow-icon" data-page="2">
                <span class="button-text"
                      data-hover-text="<?= esc_attr__( 'Load More', 'blast-2025' ) ?>">
                    <?= esc_html__( 'Load More', 'blast-2025' ) ?>
                </span>

                <span class="button-arrow-wrapper">
                    <?php sprite_svg( 'icon-arrow-right', 14, 10 ) ?>
                </span>
            </button>
        </div>

    </div><!-- /.blog-filter -->

    <script id="blog-filter-scripts" type="text/javascript">
        (function () {
            'use strict';

            document.addEventListener( 'DOMContentLoaded', function () {
                const filterContainer = document.querySelector( '.blog-filter' );
                if (!filterContainer) return;

                const grid = filterContainer.querySelector( '.blog-filter__grid' );
                const searchInput = filterContainer.querySelector( '#blog-filter-search' );
                const categoryLinks = filterContainer.querySelectorAll( '.blog-filter__category-item' );
                const loadMoreBtn = filterContainer.querySelector( '.blog-filter__load-more-btn' );
                const loadMoreContainer = filterContainer.querySelector( '.blog-filter__load-more' );
                const categoryBar = filterContainer.querySelector( '.blog-filter__bar' );

                // Get taxonomy settings from data attributes
                const taxonomy = categoryBar.getAttribute( 'data-taxonomy' ) || 'category';
                const isArchive = categoryBar.getAttribute( 'data-is-archive' ) === 'true';
                const taxId = categoryBar.getAttribute( 'data-tax-id' ) || '';

                let currentCategory = '<?= esc_js( $current_category ) ?>';
                let currentSearch = '<?= esc_js( $current_search ) ?>';
                let currentPage = 1;
                let isLoading = false;

                /**
                 * Debounce function for search input
                 */
                function debounce( func, wait ) {
                    let timeout;
                    return function executedFunction( ...args ) {
                        const later = () => {
                            clearTimeout( timeout );
                            func( ...args );
                        };
                        clearTimeout( timeout );
                        timeout = setTimeout( later, wait );
                    };
                }

                /**
                 * Update browser URL without page reload
                 */
                function updateURL( category, search ) {
                    const url = new URL( window.location.href );

                    if (category && category !== 'all') {
                        url.searchParams.set( 'category', category );
                    } else {
                        url.searchParams.delete( 'category' );
                    }

                    if (search) {
                        url.searchParams.set( 'search', search );
                    } else {
                        url.searchParams.delete( 'search' );
                    }

                    window.history.pushState( {}, '', url.toString() );
                }

                /**
                 * Load posts via AJAX
                 */
                function loadPosts( page, append = false ) {
                    if (isLoading) return;

                    isLoading = true;

                    // Add loading class
                    categoryBar.classList.add( 'blog-filter__bar--loading' );
                    if (loadMoreBtn) {
                        loadMoreBtn.classList.add( 'blog-filter__load-more-btn--loading' );
                        loadMoreBtn.disabled = true;
                    }

                    const formData = new FormData();
                    formData.append( 'action', 'blast_filter_posts' );
                    formData.append( 'page', page );
                    formData.append( 'category', currentCategory );
                    formData.append( 'search', currentSearch );
                    formData.append( 'taxonomy', taxonomy );
                    formData.append( 'is_archive', isArchive ? 'true' : 'false' );
                    formData.append( 'tax_id', taxId );

                    fetch( '<?= esc_url( admin_url( 'admin-ajax.php' ) ) ?>', {
                        method: 'POST',
                        body: formData,
                    } )
                        .then( response => response.json() )
                        .then( data => {
                            if (data.success === false) {
                                console.error( 'Filter error:', data.data );
                                return;
                            }

                            // Update grid
                            if (append) {
                                grid.insertAdjacentHTML( 'beforeend', data.html );
                            } else {
                                grid.innerHTML = data.html;
                                currentPage = 1;
                            }

                            // Show/hide Load More button
                            if (data.more_posts) {
                                loadMoreContainer.style.display = '';
                            } else {
                                loadMoreContainer.style.display = 'none';
                            }

                            // Remove loading states
                            categoryBar.classList.remove( 'blog-filter__bar--loading' );
                            if (loadMoreBtn) {
                                loadMoreBtn.classList.remove( 'blog-filter__load-more-btn--loading' );
                                loadMoreBtn.disabled = false;
                            }

                            isLoading = false;
                        } )
                        .catch( error => {
                            console.error( 'AJAX error:', error );
                            categoryBar.classList.remove( 'blog-filter__bar--loading' );
                            if (loadMoreBtn) {
                                loadMoreBtn.classList.remove( 'blog-filter__load-more-btn--loading' );
                                loadMoreBtn.disabled = false;
                            }
                            isLoading = false;
                        } );
                }

                /**
                 * Handle category filter click (toggle behavior)
                 */
                categoryLinks.forEach( link => {
                    link.addEventListener( 'click', function ( e ) {
                        e.preventDefault();

                        const category = this.getAttribute( 'data-category' );
                        const isActive = this.classList.contains( 'blog-filter__category-item--active' );

                        // Toggle behavior: if already active, deactivate (show all)
                        if (isActive) {
                            // Deactivate: remove active class and show all posts
                            this.classList.remove( 'blog-filter__category-item--active' );
                            this.blur(); // Remove focus state so it doesn't appear active
                            currentCategory = 'all';
                        } else {
                            // Activate: remove active from all, add to clicked
                            categoryLinks.forEach( l => l.classList.remove( 'blog-filter__category-item--active' ) );
                            this.classList.add( 'blog-filter__category-item--active' );
                            currentCategory = category;
                        }

                        currentPage = 1;

                        // Update URL
                        updateURL( currentCategory, currentSearch );

                        // Load posts
                        loadPosts( 1, false );
                    } );
                } );

                /**
                 * Handle search input
                 */
                const debouncedSearch = debounce( function () {
                    currentSearch = searchInput.value.trim();
                    currentPage = 1;

                    // Update URL
                    updateURL( currentCategory, currentSearch );

                    // Load posts
                    loadPosts( 1, false );
                }, 300 );

                searchInput.addEventListener( 'input', debouncedSearch );

                /**
                 * Handle load more click
                 */
                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener( 'click', function () {
                        currentPage++;
                        loadPosts( currentPage, true );
                        loadMoreBtn.setAttribute( 'data-page', currentPage + 1 );
                    } );
                }

                /**
                 * Handle browser back/forward buttons
                 */
                window.addEventListener( 'popstate', function () {
                    location.reload();
                } );
            } );
        })();
    </script>

    <?php
    return ob_get_clean();
}

add_shortcode( 'blast-blog-filter', 'blast_blog_filter_shortcode' );
