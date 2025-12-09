(function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {
        const archiveContainer = document.querySelector( '.newsroom-archive' );
        if (!archiveContainer) return;

        const grid = archiveContainer.querySelector( '.newsroom-archive__grid' );
        const loadMoreBtn = archiveContainer.querySelector( '.newsroom-archive__load-more-btn' );
        const loadMoreContainer = archiveContainer.querySelector( '.newsroom-archive__load-more' );
        const dataElement = archiveContainer.querySelector( '.newsroom-archive__data' );
        
        // Get data from attributes
        const postType = dataElement?.getAttribute('data-post-type') || 'newsroom';
        const postsPerPage = parseInt(dataElement?.getAttribute('data-posts-per-page') || 6);
        const orderBy = dataElement?.getAttribute('data-order-by') || 'date';
        const order = dataElement?.getAttribute('data-order') || 'DESC';
        const featuredIds = JSON.parse(dataElement?.getAttribute('data-featured-ids') || '[]');
        
        const loadType = 'infinite'; // 'button' or 'infinite'

        let currentPage = 1;
        let isLoading = false;
        let hasMorePosts = dataElement?.getAttribute('data-has-more') === 'true';

        

        /**
         * Load posts via AJAX
         */
        function loadPosts( page, append = false ) {
            if (isLoading) return;
            isLoading = true;

            // Add loading class
            if (loadMoreContainer) {
                loadMoreContainer.classList.add( 'newsroom-archive__load-more--loading' );

                // In infinite scroll mode, show the container during loading
                if (loadType === 'infinite' && append) {
                    loadMoreContainer.style.display = '';
                }
            }
            if (loadMoreBtn) {
                loadMoreBtn.disabled = true;
            }

            const formData = new FormData();
            formData.append( 'action', 'blast_load_archive_newsroom_posts' );
            formData.append( 'page', page );
            formData.append( 'post_type', postType );
            formData.append( 'posts_per_page', postsPerPage );
            formData.append( 'order_by', orderBy );
            formData.append( 'order', order );
            formData.append( 'featured_ids', JSON.stringify(featuredIds) );

            fetch( '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
            } )
                .then( response => response.json() )
                .then( data => {
                    if (data.success === false) {
                        console.error( 'Load error:', data.data );
                        return;
                    }

                    // Update grid - append to the list, not the whole grid
                    const listContainer = grid.querySelector( '.archive-newsroom__list' );
                    if (listContainer && append) {
                        listContainer.insertAdjacentHTML( 'beforeend', data.html );
                    } else if (listContainer) {
                        listContainer.innerHTML = data.html;
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
                    if (loadMoreContainer) {
                        loadMoreContainer.classList.remove( 'newsroom-archive__load-more--loading' );

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
                    if (loadMoreContainer) {
                        loadMoreContainer.classList.remove( 'newsroom-archive__load-more--loading' );

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
         */
        function handleInfiniteScroll() {
            if (isLoading || !hasMorePosts) {
                return;
            }

            const postItems = grid.querySelectorAll( '.newsroom-item' );
            if (postItems.length === 0) {
                return;
            }

            const lastPost = postItems[postItems.length - 1];
            const lastPostRect = lastPost.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const triggerPoint = windowHeight + 150;

            if (lastPostRect.bottom <= triggerPoint) {
                currentPage++;
                loadPosts( currentPage, true );
            }
        }

        /**
         * Initialize Infinite Scroll (if enabled)
         */
        if (loadType === 'infinite') {
            if (loadMoreBtn) {
                loadMoreBtn.style.display = 'none';
            }

            if (loadMoreContainer) {
                loadMoreContainer.style.display = 'none';
            }

            const throttledScroll = throttle( handleInfiniteScroll, 200 );
            window.addEventListener( 'scroll', throttledScroll );

            setTimeout( handleInfiniteScroll, 500 );
        }
    } );
})();