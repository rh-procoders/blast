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
function blast_blog_filter_shortcode(array $atts): string
{
    // Parse shortcode attributes
    $atts = shortcode_atts([
        'posts_per_page' => 4,
    ], $atts, 'blast-blog-filter');

    // Get current category from URL parameter
    $current_category = isset($_GET['category']) ? sanitize_key($_GET['category']) : 'all';
    $current_search   = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    // Get all categories that have posts (excluding Uncategorized)
    $categories = get_categories([
        'hide_empty' => true,
    ]);

    // Filter out Uncategorized category
    // To disable this exclusion, comment out the lines below
    $categories = array_filter($categories, function ($category) {
        return $category->slug !== 'uncategorized';
    });

    ob_start();
    ?>

    <div class="blog-filter">

        <!-- Filter Bar -->
        <div class="blog-filter__bar">

            <!-- Category Filter -->
            <?php if (!empty($categories)): ?>
            <div class="blog-filter__categories">
                <ul class="blog-filter__category-list">
                    <li>
                        <a href="?category=all"
                           class="blog-filter__category-item <?= $current_category === 'all' ? 'blog-filter__category-item--active' : '' ?>"
                           data-category="all">
                            <?= esc_html__('All', 'blast-2025') ?>
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="?category=<?= esc_attr($category->slug) ?>"
                           class="blog-filter__category-item <?= $current_category === $category->slug ? 'blog-filter__category-item--active' : '' ?>"
                           data-category="<?= esc_attr($category->slug) ?>">
                            <?= esc_html($category->name) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Search Input -->
            <div class="blog-filter__search">
                <input type="text"
                       class="blog-filter__search-input"
                       id="blog-filter-search"
                       name="blog-filter-search"
                       placeholder="<?= esc_attr__('Search articles...', 'blast-2025') ?>"
                       value="<?= esc_attr($current_search) ?>" />
                <!-- <?php /* sprite_svg('icon-search', 24, 24) */ ?> -->
            </div>

        </div><!-- /.blog-filter__bar -->

        <!-- Posts Grid -->
        <div class="blog-filter__grid">
            <?php
            // Build initial query based on URL parameters
            $args = [
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'posts_per_page' => intval($atts['posts_per_page']),
                'paged'          => 1,
            ];

            // Add search
            if (!empty($current_search)) {
                $args['s'] = $current_search;
            }

            // Exclude Uncategorized
            $uncategorized = get_category_by_slug('uncategorized');
            if ($uncategorized) {
                $args['category__not_in'] = [$uncategorized->term_id];
            }

            // Add category filter
            if ($current_category !== 'all') {
                $category = get_category_by_slug($current_category);
                if ($category) {
                    $args['category__in'] = [$category->term_id];
                }
            }

            // Run initial query
            $initial_query = new WP_Query($args);
            $has_more_posts = $initial_query->found_posts > $args['posts_per_page'];

            if ($initial_query->have_posts()):
                while ($initial_query->have_posts()):
                    $initial_query->the_post();
                    get_template_part('template-parts/components/blog-filter-item');
                endwhile;
            else:
                ?>
                <div class="blog-filter__no-results">
                    <p><?= esc_html__('No posts found.', 'blast-2025') ?></p>
                </div>
                <?php
            endif;

            wp_reset_postdata();
            ?>
        </div><!-- /.blog-filter__grid -->

        <!-- Load More Button -->
        <div class="blog-filter__load-more" style="<?= !$has_more_posts ? 'display: none;' : '' ?>">
            <button class="blog-filter__load-more-btn" data-page="2">
                <?= esc_html__('Load More', 'blast-2025') ?>
                <!-- <?php /* sprite_svg('icon-loader', 24, 24) */ ?> -->
            </button>
        </div>

    </div><!-- /.blog-filter -->

    <script id="blog-filter-scripts" type="text/javascript">
    (function() {
        'use strict';

        document.addEventListener('DOMContentLoaded', function() {
            const filterContainer = document.querySelector('.blog-filter');
            if (!filterContainer) return;

            const grid = filterContainer.querySelector('.blog-filter__grid');
            const searchInput = filterContainer.querySelector('#blog-filter-search');
            const categoryLinks = filterContainer.querySelectorAll('.blog-filter__category-item');
            const loadMoreBtn = filterContainer.querySelector('.blog-filter__load-more-btn');
            const loadMoreContainer = filterContainer.querySelector('.blog-filter__load-more');
            const categoryBar = filterContainer.querySelector('.blog-filter__bar');

            let currentCategory = '<?= esc_js($current_category) ?>';
            let currentSearch = '<?= esc_js($current_search) ?>';
            let currentPage = 1;
            let isLoading = false;

            /**
             * Debounce function for search input
             */
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            /**
             * Update browser URL without page reload
             */
            function updateURL(category, search) {
                const url = new URL(window.location.href);

                if (category && category !== 'all') {
                    url.searchParams.set('category', category);
                } else {
                    url.searchParams.delete('category');
                }

                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                window.history.pushState({}, '', url.toString());
            }

            /**
             * Load posts via AJAX
             */
            function loadPosts(page, append = false) {
                if (isLoading) return;

                isLoading = true;

                // Add loading class
                categoryBar.classList.add('blog-filter__bar--loading');
                if (loadMoreBtn) {
                    loadMoreBtn.classList.add('blog-filter__load-more-btn--loading');
                    loadMoreBtn.disabled = true;
                }

                const formData = new FormData();
                formData.append('action', 'blast_filter_posts');
                formData.append('page', page);
                formData.append('category', currentCategory);
                formData.append('search', currentSearch);

                fetch('<?= esc_url(admin_url('admin-ajax.php')) ?>', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        console.error('Filter error:', data.data);
                        return;
                    }

                    // Update grid
                    if (append) {
                        grid.insertAdjacentHTML('beforeend', data.html);
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
                    categoryBar.classList.remove('blog-filter__bar--loading');
                    if (loadMoreBtn) {
                        loadMoreBtn.classList.remove('blog-filter__load-more-btn--loading');
                        loadMoreBtn.disabled = false;
                    }

                    isLoading = false;
                })
                .catch(error => {
                    console.error('AJAX error:', error);
                    categoryBar.classList.remove('blog-filter__bar--loading');
                    if (loadMoreBtn) {
                        loadMoreBtn.classList.remove('blog-filter__load-more-btn--loading');
                        loadMoreBtn.disabled = false;
                    }
                    isLoading = false;
                });
            }

            /**
             * Handle category filter click
             */
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const category = this.getAttribute('data-category');

                    // Update active state
                    categoryLinks.forEach(l => l.classList.remove('blog-filter__category-item--active'));
                    this.classList.add('blog-filter__category-item--active');

                    // Update current category
                    currentCategory = category;
                    currentPage = 1;

                    // Update URL
                    updateURL(currentCategory, currentSearch);

                    // Load posts
                    loadPosts(1, false);
                });
            });

            /**
             * Handle search input
             */
            const debouncedSearch = debounce(function() {
                currentSearch = searchInput.value.trim();
                currentPage = 1;

                // Update URL
                updateURL(currentCategory, currentSearch);

                // Load posts
                loadPosts(1, false);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            /**
             * Handle load more click
             */
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    currentPage++;
                    loadPosts(currentPage, true);
                    loadMoreBtn.setAttribute('data-page', currentPage + 1);
                });
            }

            /**
             * Handle browser back/forward buttons
             */
            window.addEventListener('popstate', function() {
                location.reload();
            });
        });
    })();
    </script>

    <?php
    return ob_get_clean();
}

add_shortcode('blast-blog-filter', 'blast_blog_filter_shortcode');
