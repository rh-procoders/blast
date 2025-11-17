/**
 * Demo Page Scripts
 * Schedule a demo page template (looks like modal, but dedicated page)
 *
 * @package blast-2025
 */

(function() {
    'use strict';

    console.log('page-demo loaded...');

    // Back button functionality - return to referrer or homepage
    const backButton = document.querySelector('[data-demo-back]');
    if (backButton) {
        backButton.addEventListener('click', function(e) {
            e.preventDefault();

            // Get referrer (previous page)
            const referrer = document.referrer;

            // Go back to referrer if exists and is same domain, otherwise homepage
            if (referrer && referrer.includes(window.location.hostname)) {
                window.location.href = referrer;
            } else {
                window.location.href = '/';
            }
        });
    }

})();