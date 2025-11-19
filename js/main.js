/**
 * @author: Renato Hoxha <renato@procoders.tech>
 * @description: jQuery global Functions.
 * @version: 1.0.0
 */
(function(){

    // Create cross browser requestAnimationFrame method:
    window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || function(f) {
        setTimeout(f, 1000 / 60);
    };


    //Mobile menu toggle
    function handleMenuToggle() {
        const menuToggle = document.getElementById('menu_toggle_button');
        menuToggle.addEventListener('click', () => {
            document.getElementById('masthead').classList.toggle('active');
        });

        
    }


    // DOCUMENT READY //
    document.addEventListener("DOMContentLoaded", () => {
        "use strict";

        $('.menu').on('click', '.menu-item-has-children > a', function(e){
            e.preventDefault();
            $('.site-navigation').find('.open-submenu').not($(this).parents('.menu-item-has-children')).removeClass('open-submenu');
            $('.site-navigation').find('.sub-menu').not($(this).parents('.menu-item-has-children').find('.sub-menu')).slideUp();    
            $(this).parents('.menu-item-has-children').toggleClass('open-submenu');
            $(this).parents('.menu-item-has-children').find('.sub-menu').slideToggle();
  
        });

        // Initialize Fancybox for WordPress gallery blocks
        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind('[data-fancybox="gallery"]', {
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [
                            "zoomIn",
                            "zoomOut",
                            "toggle1to1"
                        ],
                        right: ["slideshow", "close"],
                    },
                },
                Thumbs: {
                    autoStart: false,
                },
            });
        }
        setTimeout(() => {
            console.log('Showing popup modal');
            $('body').find('.popup-modal').fadeIn();
        }, 5000);

        $('body').on('click', '.popup-modal__close-button-js-toggle', function(e){
            e.preventDefault();
            $(this).parents('.popup-modal').fadeOut();
        })

        handleMenuToggle();

    });

    document.addEventListener("DOMContentLoaded", function() {
    const heading = document.querySelector(".grid__col--left");
    const toc = document.getElementById("ez-toc-container"); // your TOC div
        if (heading && toc) {
            heading.insertAdjacentElement("afterbegin", toc);
        }
    });

    function isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
      }
      
      function handleScroll() {
        const elements = document.querySelectorAll('.circle-animation');
        elements.forEach(el => {
          if (isInViewport(el)) {
            el.classList.add('active');
          }
        });
      }
      
      // Run on scroll and also on load
      window.addEventListener('scroll', handleScroll);
      window.addEventListener('load', handleScroll);

    /**
     * Initialize SimpleBar on CookieYes modal
     * Works with both static and dynamically injected CookieYes modals
     */
    function initCookieYesSimpleBar() {
        const wrapper = document.querySelector('.cky-preference-body-wrapper');

        if (wrapper && !wrapper.hasAttribute('data-simplebar')) {
            // Add classes and attributes
            wrapper.classList.add('using-simplebar');
            wrapper.setAttribute('data-simplebar', '');
            wrapper.setAttribute('data-simplebar-auto-hide', 'false');

            // Initialize SimpleBar if available
            if (typeof SimpleBar !== 'undefined') {
                new SimpleBar(wrapper);
            }
        }
    }

    // Try to initialize immediately on load
    window.addEventListener('load', function() {
        setTimeout(initCookieYesSimpleBar, 100);
    });

    // Use MutationObserver to watch for CookieYes modal injection
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        // Check if the added node contains cky-preference-body-wrapper
                        if (node.classList && node.classList.contains('cky-preference-body-wrapper')) {
                            initCookieYesSimpleBar();
                        } else if (node.querySelector && node.querySelector('.cky-preference-body-wrapper')) {
                            initCookieYesSimpleBar();
                        }
                    }
                });
            }
        });
    });

    // Start observing when DOM is ready
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();
