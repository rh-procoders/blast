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
            'post_type'      => 'post',      // Post type: post, events, newsroom
            'tax'            => '',          // Taxonomy type: category, tag, author, event_types (auto-detected if empty)
            'is_archive'     => 'false',     // Archive mode flag
            'tax_id'         => null,        // Specific taxonomy ID to filter
            'load_type'      => 'button',    // Load type: button|infinite
    ], $atts, 'blast-blog-filter' );

    // Sanitize post type
    $post_type  = sanitize_key( $atts['post_type'] );

    // Auto-detect taxonomy based on post type if not explicitly set
    $taxonomy = ! empty( $atts['tax'] ) ? sanitize_key( $atts['tax'] ) : '';

    if ( empty( $taxonomy ) ) {
        // Post type to taxonomy mapping
        $taxonomy_map = [
            'post'     => 'category',
            'events'   => 'event_types',
            'newsroom' => 'category',  // Change if newsroom has custom taxonomy
        ];

        // Use mapped taxonomy or attempt to auto-detect
        if ( isset( $taxonomy_map[ $post_type ] ) ) {
            $taxonomy = $taxonomy_map[ $post_type ];
        } else {
            // Fallback: get first registered taxonomy for this post type
            $taxonomies = get_object_taxonomies( $post_type, 'names' );
            $taxonomy   = ! empty( $taxonomies ) ? $taxonomies[0] : 'category';
        }
    }

    // Get the URL-friendly parameter name for this taxonomy
    $taxonomy_param = blast_get_taxonomy_param_name( $taxonomy );

    // Get current term from URL parameter (use dynamic parameter based on taxonomy)
    $current_term = isset( $_GET[ $taxonomy_param ] ) ? sanitize_key( $_GET[ $taxonomy_param ] ) : 'all';
    $current_search   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

    $is_archive = $atts['is_archive'] === 'true';
    $tax_id     = ! empty( $atts['tax_id'] ) ? absint( $atts['tax_id'] ) : null;

    // Get taxonomy terms based on taxonomy type (only if not filtering by tax_id)
    $terms = [];
    if ( ! $tax_id && $taxonomy !== 'author' ) {
        // Use general get_terms() for all taxonomies
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ] );

        // Filter out Uncategorized category (only for category taxonomy)
        if ( $taxonomy === 'category' && ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $terms = array_filter( $terms, function ( $term ) {
                return $term->slug !== 'uncategorized';
            } );
        }

        // Ensure $terms is array (in case of WP_Error)
        if ( is_wp_error( $terms ) ) {
            $terms = [];
        }
    }
    // Note: Author taxonomy handled differently (no filter UI for authors)

    ob_start();
    ?>

    <div class="blog-filter">

        <!-- Filter Bar -->
        <div class="blog-filter__bar container container--blog-filter"
             data-post-type="<?= esc_attr( $post_type ) ?>"
             data-taxonomy="<?= esc_attr( $taxonomy ) ?>"
             data-taxonomy-param="<?= esc_attr( $taxonomy_param ) ?>"
             data-is-archive="<?= esc_attr( $is_archive ? 'true' : 'false' ) ?>"
             data-tax-id="<?= esc_attr( $tax_id ?? '' ) ?>"
             data-load-type="<?= esc_attr( sanitize_key( $atts['load_type'] ) ) ?>">

            <!-- Search Input -->
            <div class="blog-filter__search">
                <input type="text"
                       class="blog-filter__search-input"
                       id="blog-filter-search"
                       name="blog-filter-search"
                       placeholder="<?= esc_attr__( 'Search', 'blast-2025' ) ?>"
                       value="<?= esc_attr( $current_search ) ?>"/>
                <button type="button"
                        class="blog-filter__search-clear"
                        aria-label="<?= esc_attr__( 'Clear search', 'blast-2025' ) ?>"
                        style="<?= empty( $current_search ) ? 'display: none;' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M11.0625 11.0625L1.06271 1.0625" fill="none" stroke-width="1.5"
                              stroke-linecap="square"/>
                        <path d="M1.0625 11.0625L11.0623 1.0625" fill="none" stroke-width="1.5"
                              stroke-linecap="square"/>
                    </svg>
                </button>
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
                                <a href="?<?= esc_attr( $taxonomy_param ) ?>=<?= esc_attr( $term->slug ) ?>"
                                   class="blog-filter__category-item <?= $current_term === $term->slug ? 'blog-filter__category-item--active' : '' ?>"
                                   data-term="<?= esc_attr( $term->slug ) ?>">
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
                    'post_type'      => $post_type,
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
                } else {
                    // General handling for any custom taxonomy
                    $args['tax_query'] = [
                        [
                            'taxonomy' => $taxonomy,
                            'field'    => 'term_id',
                            'terms'    => $tax_id,
                        ],
                    ];
                }
            } elseif ( $current_term !== 'all' ) {
                // Filter by URL parameter (normal mode)
                if ( $taxonomy === 'category' ) {
                    $category = get_category_by_slug( $current_term );
                    if ( $category ) {
                        $args['category__in'] = [ $category->term_id ];
                    }
                } elseif ( $taxonomy === 'tag' ) {
                    $tag = get_term_by( 'slug', $current_term, 'post_tag' );
                    if ( $tag ) {
                        $args['tag_id'] = $tag->term_id;
                    }
                } else {
                    // General handling for any custom taxonomy
                    $term = get_term_by( 'slug', $current_term, $taxonomy );
                    if ( $term ) {
                        $args['tax_query'] = [
                            [
                                'taxonomy' => $taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $term->term_id,
                            ],
                        ];
                    }
                }
            }

            // Run initial query
            $initial_query  = new WP_Query( $args );
            $has_more_posts = $initial_query->found_posts > $args['posts_per_page'];

            if ( $initial_query->have_posts() ):
                while ($initial_query->have_posts()):
                    $initial_query->the_post();

                    if ( $post_type === 'events' ) {
                        get_template_part( 'template-parts/components/event-filter-item' );
                    } else {
                        get_template_part( 'template-parts/components/blog-filter-item' );
                    }

                endwhile;
            else:
                ?>
                <div class="blog-filter__no-results">
                    <p><?= esc_html__( 'No articles found...', 'blast-2025' ) ?></p>
                </div>
            <?php
            endif;

            wp_reset_postdata();
            ?>
        </div><!-- /.blog-filter__grid -->

        <!-- Load More Button -->
        <div class="blog-filter__load-more wp-block-button is-style-outline is-style-outline--1"
             style="<?= ! $has_more_posts ? 'display: none;' : '' ?>">
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
                const searchClearBtn = filterContainer.querySelector( '.blog-filter__search-clear' );
                const categoryLinks = filterContainer.querySelectorAll( '.blog-filter__category-item' );
                const loadMoreBtn = filterContainer.querySelector( '.blog-filter__load-more-btn' );
                const loadMoreContainer = filterContainer.querySelector( '.blog-filter__load-more' );
                const categoryBar = filterContainer.querySelector( '.blog-filter__bar' );

                // Get taxonomy settings from data attributes
                const postType = categoryBar.getAttribute( 'data-post-type' ) || 'post';
                const taxonomy = categoryBar.getAttribute( 'data-taxonomy' ) || 'category';
                const taxonomyParam = categoryBar.getAttribute( 'data-taxonomy-param' ) || 'category';
                const isArchive = categoryBar.getAttribute( 'data-is-archive' ) === 'true';
                const taxId = categoryBar.getAttribute( 'data-tax-id' ) || '';
                const loadType = categoryBar.getAttribute( 'data-load-type' ) || 'button';

                let currentTerm = '<?= esc_js( $current_term ) ?>';
                let currentSearch = '<?= esc_js( $current_search ) ?>';
                let currentPage = 1;
                let isLoading = false;
                let hasMorePosts = <?= $has_more_posts ? 'true' : 'false' ?>;

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
                 * Uses dynamic taxonomy parameter name for semantic URLs
                 */
                function updateURL( termSlug, search ) {
                    const url = new URL( window.location.href );

                    // Use dynamic taxonomy parameter (e.g., 'event_type' for events, 'category' for posts)
                    if (termSlug && termSlug !== 'all') {
                        url.searchParams.set( taxonomyParam, termSlug );
                    } else {
                        url.searchParams.delete( taxonomyParam );
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
                    if (loadMoreContainer) {
                        loadMoreContainer.classList.add( 'blog-filter__load-more--loading' );

                        // In infinite scroll mode, show the container during loading
                        if (loadType === 'infinite' && append) {
                            loadMoreContainer.style.display = '';
                        }
                    }
                    if (loadMoreBtn) {
                        loadMoreBtn.disabled = true;
                    }

                    const formData = new FormData();
                    formData.append( 'action', 'blast_filter_posts' );
                    formData.append( 'page', page );
                    formData.append( 'post_type', postType );
                    formData.append( 'term', currentTerm );
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

                            // Update hasMorePosts flag
                            hasMorePosts = data.more_posts;

                            // Show/hide Load More button (only in button mode)
                            if (loadType === 'button') {
                                if (data.more_posts) {
                                    loadMoreContainer.style.display = '';
                                } else {
                                    loadMoreContainer.style.display = 'none';
                                }
                            }

                            // Remove loading states
                            categoryBar.classList.remove( 'blog-filter__bar--loading' );
                            if (loadMoreContainer) {
                                loadMoreContainer.classList.remove( 'blog-filter__load-more--loading' );

                                // In infinite scroll mode, hide the container after loading completes
                                if (loadType === 'infinite') {
                                    loadMoreContainer.style.display = 'none';
                                }
                            }
                            if (loadMoreBtn) {
                                loadMoreBtn.disabled = false;
                            }

                            isLoading = false;
                        } )
                        .catch( error => {
                            console.error( 'AJAX error:', error );
                            categoryBar.classList.remove( 'blog-filter__bar--loading' );
                            if (loadMoreContainer) {
                                loadMoreContainer.classList.remove( 'blog-filter__load-more--loading' );

                                // In infinite scroll mode, hide the container after error
                                if (loadType === 'infinite') {
                                    loadMoreContainer.style.display = 'none';
                                }
                            }
                            if (loadMoreBtn) {
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

                        const termSlug = this.getAttribute( 'data-term' );
                        const isActive = this.classList.contains( 'blog-filter__category-item--active' );

                        // Toggle behavior: if already active, deactivate (show all)
                        if (isActive) {
                            // Deactivate: remove active class and show all posts
                            this.classList.remove( 'blog-filter__category-item--active' );
                            this.blur(); // Remove focus state so it doesn't appear active
                            currentTerm = 'all';
                        } else {
                            // Activate: remove active from all, add to clicked
                            categoryLinks.forEach( l => l.classList.remove( 'blog-filter__category-item--active' ) );
                            this.classList.add( 'blog-filter__category-item--active' );
                            currentTerm = termSlug;
                        }

                        currentPage = 1;

                        // Update URL
                        updateURL( currentTerm, currentSearch );

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
                    updateURL( currentTerm, currentSearch );

                    // Load posts
                    loadPosts( 1, false );
                }, 300 );

                searchInput.addEventListener( 'input', function () {
                    // Show/hide clear button based on input value
                    if (searchClearBtn) {
                        if (searchInput.value.trim().length > 0) {
                            searchClearBtn.style.display = '';
                        } else {
                            searchClearBtn.style.display = 'none';
                        }
                    }

                    // Trigger debounced search
                    debouncedSearch();
                } );

                /**
                 * Handle search clear button click
                 */
                if (searchClearBtn) {
                    searchClearBtn.addEventListener( 'click', function () {
                        // Only proceed if there was text to clear
                        const hadText = searchInput.value.trim().length > 0;

                        // Clear the input
                        searchInput.value = '';

                        // Hide the clear button
                        searchClearBtn.style.display = 'none';

                        // If there was text, trigger search to update posts
                        if (hadText) {
                            currentSearch = '';
                            currentPage = 1;

                            // Update URL
                            updateURL( currentTerm, currentSearch );

                            // Load posts
                            loadPosts( 1, false );
                        }

                        // Focus back on input for better UX
                        searchInput.focus();
                    } );
                }

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
                 * Throttle function for scroll events
                 * Unlike debounce, throttle ensures the function runs at regular intervals
                 */
                function throttle( func, wait ) {
                    let timeout = null;
                    let previous = 0;

                    return function executedFunction( ...args ) {
                        const now = Date.now();
                        const remaining = wait - (now - previous);

                        if (remaining <= 0 || remaining > wait) {
                            if (timeout) {
                                clearTimeout( timeout );
                                timeout = null;
                            }
                            previous = now;
                            func( ...args );
                        } else if (!timeout) {
                            timeout = setTimeout( function () {
                                previous = Date.now();
                                timeout = null;
                                func( ...args );
                            }, remaining );
                        }
                    };
                }

                /**
                 * Infinite Scroll Handler
                 * Checks if user scrolled near the bottom of the last post
                 */
                function handleInfiniteScroll() {
                    // Don't load if already loading or no more posts
                    if (isLoading || !hasMorePosts) {
                        return;
                    }

                    // Get all post items
                    const postItems = grid.querySelectorAll( '.blog-filter-item, .related-post-item' );
                    if (postItems.length === 0) {
                        return;
                    }

                    // Get the last post item
                    const lastPost = postItems[postItems.length - 1];
                    const lastPostRect = lastPost.getBoundingClientRect();

                    // Get window height
                    const windowHeight = window.innerHeight;

                    // Calculate trigger point: 150px before the bottom of last post enters viewport
                    // lastPostRect.bottom = distance from top of viewport to bottom of element
                    // If bottom of last post is less than windowHeight + 150px, trigger load
                    const triggerPoint = windowHeight + 150;

                    if (lastPostRect.bottom <= triggerPoint) {
                        // User has scrolled near the last post, load more
                        currentPage++;
                        loadPosts( currentPage, true );
                    }
                }

                /**
                 * Initialize Infinite Scroll (if enabled)
                 */
                if (loadType === 'infinite') {
                    // Hide the actual button in infinite scroll mode (keep container for loader)
                    if (loadMoreBtn) {
                        loadMoreBtn.style.display = 'none';
                    }

                    // Hide the container initially
                    if (loadMoreContainer) {
                        loadMoreContainer.style.display = 'none';
                    }

                    // Add throttled scroll listener (checks every 200ms)
                    const throttledScroll = throttle( handleInfiniteScroll, 200 );
                    window.addEventListener( 'scroll', throttledScroll );

                    // Also check on initial load (in case content doesn't fill viewport)
                    setTimeout( handleInfiniteScroll, 500 );
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
