/**
 * Product Animation Content Block - Lottie & Hover Interactions
 */

(function() {
    'use strict';

    // Polyfill for closest() method if not supported
    if (!Element.prototype.closest) {
        Element.prototype.closest = function(s) {
            var el = this;
            do {
                if (Element.prototype.matches.call(el, s)) return el;
                el = el.parentElement || el.parentNode;
            } while (el !== null && el.nodeType === 1);
            return null;
        };
    }

    // Polyfill for matches() method if not supported
    if (!Element.prototype.matches) {
        Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
    }

    // Helper function to find closest element
    function findClosest(element, selector) {
        if (!element) return null;
        if (element.closest) {
            return element.closest(selector);
        }
        // Fallback for older browsers
        while (element) {
            if (element.matches && element.matches(selector)) {
                return element;
            }
            element = element.parentElement;
        }
        return null;
    }

    // Initialize Lottie animations when DOM is ready
    function initLottieAnimations() {
        const lottieElements = document.querySelectorAll('.pac-lottie');
        
        if (lottieElements.length === 0) {
            return;
        }

        lottieElements.forEach((element) => {
            // Ensure lottie-player is ready and starts playing
            if (element.tagName.toLowerCase() === 'lottie-player') {
                // Lottie-player web component handles autoplay automatically
                // Force play if autoplay attribute is set
                if (element.hasAttribute('autoplay')) {
                    element.play();
                }
            }
        });
    }

    // Handle content dot hover interactions
    function initDotInteractions() {
        const contentDots = document.querySelectorAll('.pac-content-dot');

        contentDots.forEach((dot) => {
            const closeBtn = dot.querySelector('.pac-dot-close');
            const contentBox = dot.querySelector('.pac-dot-content');

            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dot.classList.remove('active');
                    if (contentBox) {
                        contentBox.style.display = 'none';
                    }
                });
            }

            // Keyboard support - close on ESC
            dot.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    dot.classList.remove('active');
                    if (contentBox) {
                        contentBox.style.display = 'none';
                    }
                }
            });
        });

        // Close content when clicking outside
        document.addEventListener('click', (e) => {
            const dotsContainer = findClosest(e.target, '.pac-dots-container');
            
            if (!dotsContainer) {
                contentDots.forEach((dot) => {
                    dot.classList.remove('active');
                    const contentBox = dot.querySelector('.pac-dot-content');
                    if (contentBox) {
                        contentBox.style.display = 'none';
                    }
                });
            }
        });
    }

    // Handle responsive positioning of tooltips
    function adjustTooltipPosition() {
        const contentDots = document.querySelectorAll('.pac-content-dot');

        contentDots.forEach((dot) => {
            const contentBox = dot.querySelector('.pac-dot-content');
            if (!contentBox) return;

            // Get viewport info
            const rect = contentBox.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            // Adjust horizontal position if needed
            if (rect.right > viewportWidth - 20) {
                contentBox.style.right = '0';
                contentBox.style.left = 'auto';
            }

            // Adjust vertical position if needed
            if (rect.bottom > viewportHeight - 20) {
                contentBox.style.bottom = '80px';
                contentBox.style.top = 'auto';
            }
        });
    }

    // Debounce resize events
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

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initLottieAnimations();
            initDotInteractions();
        });
    } else {
        initLottieAnimations();
        initDotInteractions();
    }

    // Reinitialize on window resize for responsive tooltip positioning
    window.addEventListener('resize', debounce(adjustTooltipPosition, 150));

    // Also adjust on first hover
    document.addEventListener('mouseenter', (e) => {
        if (findClosest(e.target, '.pac-content-dot')) {
            setTimeout(adjustTooltipPosition, 50);
        }
    }, true);

})();
