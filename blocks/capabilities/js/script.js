/**
 * Capabilities Block - JavaScript
 */

(function() {
    'use strict';

    // Initialize block interactions
    function initCapabilities() {
        // Add any interactive functionality here if needed
        console.log('Capabilities block initialized');
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCapabilities);
    } else {
        initCapabilities();
    }

})();
