/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 *
 * This file will be auto-enqueued
 *
 * @package blast_Wp
 */

(function () {
    const masthead = document.getElementById( 'masthead' );
    const siteNavigation = document.getElementById( 'site-navigation' );
    const body = document.body;

    // Return early if the navigation doesn't exist.
    if (!siteNavigation) {
        return;
    }

    const menuTopTitle = document.querySelector( '.menu-top-title' );
    const button = document.querySelector( '.menu-toggle' );
    const closeButton = siteNavigation.querySelector( '.mobile-menu-top__close' );
    const backButton = siteNavigation.querySelector( '.mobile-menu-top__back' );

    // Return early if the button doesn't exist.
    if (!button) {
        return;
    }

    const menu = siteNavigation.getElementsByTagName( 'ul' )[0];

    // Hide menu toggle button if menu is empty and return early.
    if ('undefined' === typeof menu) {
        button.style.display = 'none';
        return;
    }

    if (!menu.classList.contains( 'nav-menu' )) {
        menu.classList.add( 'nav-menu' );
    }

    // Add "Menu" title for mobile at the top of the navigation
    const mobileMenuTitle = document.createElement( 'div' );
    mobileMenuTitle.classList.add( 'mobile-menu-main-title' );
    mobileMenuTitle.textContent = 'Menu';
    menu.insertBefore( mobileMenuTitle, menu.firstChild );

    // Toggle the .toggled class and the aria-expanded value each time the button is clicked.
    button.addEventListener(
        'click',
        function () {

            siteNavigation.classList.toggle( 'toggled' );
            console.log('Navigation toggled, has toggled class:', siteNavigation.classList.contains('toggled'));

            const isExpanded = button.getAttribute( 'aria-expanded' ) === 'true';

            if (isExpanded) {
                button.setAttribute( 'aria-expanded', 'false' );
                document.documentElement.classList.remove( 'no-scroll' );
            } else {
                button.setAttribute( 'aria-expanded', 'true' );
                document.documentElement.classList.add( 'no-scroll' );
            }
        }
    );

    // Add event listeners to ALL menu top titles, not just the first one (mobile only)
    document.querySelectorAll('.menu-top-title').forEach(function(menuTopTitle) {
        menuTopTitle.addEventListener(
            'click',
            function () {
                // Only close menus on mobile (1024px and below)
                if (window.matchMedia('(max-width: 1024px)').matches) {
                    // Close all open mega menus first
                    document.querySelectorAll('.menu-item.is-mega-menu .mega-columns.mega-open')
                        .forEach(openMenu => {
                            openMenu.classList.remove('mega-open');
                            openMenu.closest('.menu-item.is-mega-menu').classList.remove('mega-menu-active');
                        });
                }
            }
        );
    });

    if (closeButton) {
        closeButton.addEventListener(
            'click',
            function () {

                siteNavigation.classList.remove( 'toggled' );
                button.setAttribute( 'aria-expanded', 'false' );
                document.documentElement.classList.remove( 'no-scroll' );
            }
        );
    }

    if (backButton) {
        backButton.addEventListener(
            'click',
            function () {

                // Find the closest parent with the class 'sub-megamenu-open' and remove it
                const parentMenu = this.closest( '.sub-megamenu-open' );
                if (parentMenu) {
                    parentMenu.classList.remove( 'sub-megamenu-open' );
                }
            }
        );
    }

    // Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
    document.addEventListener(
        'click',
        function ( event ) {
            const isClickInside = siteNavigation.contains( event.target ) || button.contains( event.target );

            if (!isClickInside) {
                siteNavigation.classList.remove( 'toggled' );
                button.setAttribute( 'aria-expanded', 'false' );
                document.documentElement.classList.remove( 'no-scroll' );
            }
        }
    );

    // Get all the link elements within the menu.
    const links = menu.querySelectorAll( '.menu-link' );


    // Get all the link elements with children within the menu.
    const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

    // Toggle focus each time a menu link is focused or blurred.
    for (const link of links) {
        link.addEventListener( 'focus', toggleFocus, true );
        link.addEventListener( 'click', toggleFocus, true );
        link.addEventListener( 'blur', toggleFocus, true );
    }

    // Toggle focus each time a menu link with children receive a touch event.
    for (const link of linksWithChildren) {
        link.addEventListener( 'touchstart', toggleFocus, false );
    }

    /**
     * Sets or removes .focus class on an element.
     */
    function toggleFocus( event ) {
        if (event.type === 'focus' || event.type === 'blur' || event.type === 'click') {
            let self = this;
            // Move up through the ancestors of the current link until we hit .nav-menu.
            while (!self.classList.contains( 'nav-menu' )) {
                // On li elements toggle the class .focus.
                if ('li' === self.tagName.toLowerCase()) {
                    self.classList.toggle( 'focus' );
                }
                self = self.parentNode;
            }
        }

        if (event.type === 'touchstart') {
            const menuItem = this.parentNode;
            event.preventDefault();
            for (const link of menuItem.parentNode.children) {
                if (menuItem !== link) {
                    link.classList.remove( 'focus' );
                }
            }
            menuItem.classList.toggle( 'focus' );
        }
    }

    // Header animation on scroll
    const topBar = document.querySelector( '.top-bar-block' );
    const header = document.getElementById( "masthead" );

    // Add 'top-positioned' class if the user is at the top of the page initially
    if (scrollY === 0) {
        header.classList.add( "top-positioned" );
    }

    // Scroll Event
    let lastY = scrollY;

    const MAX_SCROLL_DELTA = 5, NAV_HEIGHT = header.offsetHeight;

    /* ── use a safe fallback ─────────────────────────────────── */
    // 0 when .top-bar-block isn’t present
    const TOP_BAR_HEIGHT = topBar ? topBar.offsetHeight : 0;
    /* ────────────────────────────────────────────────────────── */

    addEventListener( "scroll", () => {

        const delta = scrollY - lastY;


        // Add or remove 'hiddeNavBar' class based on scroll direction
        if (Math.abs( delta ) > MAX_SCROLL_DELTA) {
            header.classList.toggle( "hiddeNavBar", delta > 0 && scrollY > NAV_HEIGHT + TOP_BAR_HEIGHT );
            lastY = scrollY;

            // Set the 'top' property of the inline style
            if (header.classList.contains( 'hiddeNavBar' )) {
                header.style.position = `fixed`;
            }

            if (scrollY < TOP_BAR_HEIGHT) {
                header.style.position = `absolute`;
            }
            if (scrollY < NAV_HEIGHT + TOP_BAR_HEIGHT) {
                header.classList.remove( "header-top-animation" );
            } else {
                header.classList.add( "header-top-animation" );
            }
        }

        // Add or remove 'top-positioned' class based on scroll position
        if (scrollY === 0) {
            header.classList.add( "top-positioned" );
        } else {
            header.classList.remove( "top-positioned" );
        }
    } );


    document.addEventListener('DOMContentLoaded', function () {
        const menuItems = document.querySelectorAll('.menu-item.is-mega-menu');

        // Query normal (non-megamenu) menu items
        const normalItems = document.querySelectorAll('.menu-item.is-not-mega');

        // --- Shared close timer ---
        let closeTimer = null;
        const scheduleClose = (ms) => {
            if (closeTimer) clearTimeout(closeTimer);

            closeTimer = setTimeout(() => {
                document.querySelectorAll('.menu-item.is-mega-menu .mega-columns.mega-open')
                    .forEach(openMenu => {
                        openMenu.classList.remove('mega-open');
                        openMenu.closest('.menu-item.is-mega-menu').classList.remove('mega-menu-active');
                    });
            }, ms);
        };

        const cancelScheduledClose = () => {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
        };

        // --- Normal items: close instantly on hover (desktop only) ---
        normalItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                if (!isTouchDevice()) {
                    scheduleClose(0); // immediate close
                }
            });
        });

        menuItems.forEach(item => {
            const link = item.querySelector('span.menu-link');
            const megaMenu = item.querySelector('.mega-columns');


            if (!megaMenu || !link) return;

            // --- Hover Support (Desktop) ---
            item.addEventListener('mouseenter', () => {
                if (!isTouchDevice()) {
                    // Cancel any scheduled close
                    cancelScheduledClose();

                    // Close all open mega menus first
                    document.querySelectorAll('.menu-item.is-mega-menu .mega-columns.mega-open')
                        .forEach(openMenu => {
                            openMenu.classList.remove('mega-open');
                            openMenu.closest('.menu-item.is-mega-menu').classList.remove('mega-menu-active');
                        });

                    // Open the current one
                    megaMenu.classList.add('mega-open');
                    item.classList.add('mega-menu-active');
                }
            });

            // --- Click Toggle (Touch Devices Only) ---
            link.addEventListener('click', function (e) {

                    e.preventDefault();

                    // Close all open mega menus first
                    document.querySelectorAll('.menu-item.is-mega-menu .mega-columns.mega-open')
                        .forEach(openMenu => {
                            openMenu.classList.remove('mega-open');
                            openMenu.closest('.menu-item.is-mega-menu').classList.remove('mega-menu-active');
                        });

                    // Toggle class name to parent for mobile usage

                    siteNavigation.classList.toggle('sub-megamenu-open');
                    // Toggle current
                    megaMenu.classList.add('mega-open');
                    item.classList.add('mega-menu-active');

            });
        });

        // --- Global mouseover: delayed close when hovering outside megamenu areas (desktop only) ---
        document.addEventListener('mouseover', (e) => {
            if (isTouchDevice()) return;

            // If hovering a normal item, we've already handled instant close above
            if (e.target.closest('.menu-item.is-not-mega')) return;

            // If not inside a megamenu item or its panel, schedule delayed close
            if (!e.target.closest('.menu-item.is-mega-menu') && !e.target.closest('.mega-columns')) {
                scheduleClose(250); // delayed close after 500ms
            }
        });

        // --- Navigation mouseleave: delayed close when leaving the nav (desktop only) ---
        siteNavigation.addEventListener('mouseleave', () => {
            if (!isTouchDevice()) {
                scheduleClose(250); // delayed close after 500ms
            }
        });

        // --- Outside Click to Close ---
        document.addEventListener('click', function (e) {

            if (!e.target.closest('.menu-item.is-mega-menu')) {

                menuItems.forEach(item => {

                    const megaMenuOpen = item.querySelector('.mega-columns');
                    megaMenuOpen.classList.remove('mega-open');
                    item.classList.remove('mega-menu-active');

                });
            }
        });

        // --- Helper: Detect Touch Device ---
        function isTouchDevice() {
            return window.matchMedia('(pointer: coarse)').matches;
        }
    });

    let lastScrollTop = 0;

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        if (scrollTop > 20) {
            document.querySelectorAll('.menu-item.is-mega-menu .mega-columns.mega-open')
                        .forEach(openMenu => {
                            openMenu.classList.remove('mega-open');
                            openMenu.closest('.menu-item.is-mega-menu').classList.remove('mega-menu-active');
                        });
        }
    });


}());
