/**
 * Demo Page Scripts
 * Schedule a demo page template (looks like modal, but dedicated page)
 *
 * @package blast-2025
 */

(function () {
    'use strict';

    console.log( 'page-demo loaded...' );

    /**
     * Show Success Screen
     * Called from CF7's on_sent_ok additional setting
     * Adds modifier class to show success content and hide form/content
     */
    window.demoShowSuccess = function () {
        const demoSection = document.querySelector( '.demo-section' );

        if (demoSection) {
            // Add success modifier class
            demoSection.classList.add( 'demo-section--success' );

            // Optional: Smooth scroll to center the success message
            demoSection.scrollIntoView( {
                behavior: 'smooth',
                block: 'center'
            } );

            console.log( 'Success screen activated' );
        } else {
            console.error( 'demoShowSuccess: .demo-section not found' );
        }
    };

    // Back button functionality - return to referrer or homepage
    const backButton = document.querySelector( '[data-demo-back]' );
    if (backButton) {
        backButton.addEventListener( 'click', function ( e ) {
            e.preventDefault();

            // Get referrer (previous page)
            const referrer = document.referrer;

            // Go back to referrer if exists and is same domain, otherwise homepage
            if (referrer && referrer.includes( window.location.hostname )) {
                window.location.href = referrer;
            } else {
                window.location.href = '/';
            }
        } );
    }

    /**
     * CF7 Multi-Step Progress Bar Controller
     *
     * Updates the progress bar when user navigates between form steps
     */
    function initProgressBar() {
        const progressBar = document.querySelector( '.cf7mls-progress-bar' );

        if (!progressBar) {
            return; // No progress bar, exit early
        }

        const currentStepElement = progressBar.querySelector( '.cf7mls-progress-bar__current' );
        const totalStepElement = progressBar.querySelector( '.cf7mls-progress-bar__total' );
        const progressFill = progressBar.querySelector( '.cf7mls-progress-bar__fill' );
        const wrapper = document.querySelector( '.fieldset-cf7mls-wrapper' );

        if (!currentStepElement || !totalStepElement || !progressFill || !wrapper) {
            return; // Missing required elements
        }

        /**
         * Update the progress bar current step number and fill width
         */
        function updateProgressBar() {
            // Find all fieldsets
            const fieldsets = wrapper.querySelectorAll( '.fieldset-cf7mls' );

            if (fieldsets.length === 0) {
                return;
            }

            const totalSteps = fieldsets.length;

            // Find which fieldset is currently active (has cf7mls_current_fs class)
            let currentStep = 1;
            fieldsets.forEach( ( fieldset, index ) => {
                if (fieldset.classList.contains( 'cf7mls_current_fs' )) {
                    currentStep = index + 1;
                }
            } );

            // Calculate percentage
            const percentage = (currentStep / totalSteps) * 100;

            // Update the current step display
            currentStepElement.textContent = currentStep;

            // Update the visual bar fill width
            progressFill.style.width = percentage + '%';
            progressFill.setAttribute( 'data-progress', currentStep );

            console.log( 'Progress bar updated: Step', currentStep, 'of', totalSteps, '(' + percentage.toFixed( 2 ) + '%)' );
        }

        // Initial update on page load
        updateProgressBar();

        // Listen for clicks on Next/Back buttons
        const nextButtons = document.querySelectorAll( '.cf7mls_next' );
        const backButtons = document.querySelectorAll( '.cf7mls_back' );

        nextButtons.forEach( button => {
            button.addEventListener( 'click', function () {
                // Update after a short delay to allow the plugin to change the active step
                setTimeout( updateProgressBar, 100 );
            } );
        } );

        backButtons.forEach( button => {
            button.addEventListener( 'click', function () {
                // Update after a short delay to allow the plugin to change the active step
                setTimeout( updateProgressBar, 100 );
            } );
        } );

        // Fallback: Use MutationObserver to detect class changes on fieldsets
        // This catches step changes even if button events don't fire
        const observer = new MutationObserver( function ( mutations ) {
            mutations.forEach( function ( mutation ) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    updateProgressBar();
                }
            } );
        } );

        // Observe all fieldsets for class changes
        const fieldsets = wrapper.querySelectorAll( '.fieldset-cf7mls' );
        fieldsets.forEach( fieldset => {
            observer.observe( fieldset, {
                attributes: true,
                attributeFilter: ['class']
            } );
        } );

        console.log( 'CF7 Multi-Step progress bar initialized' );
    }

    /**
     * CF7 Form Submission Success Handler
     * Listens for successful form submission and shows success screen
     */
    function initSuccessHandler() {
        // Listen for CF7 mail sent event (only on this page's form)
        document.addEventListener( 'wpcf7mailsent', function ( event ) {
            // Verify the form is within our demo-section
            const demoSection = event.target.closest( '.demo-section' );

            if (demoSection) {
                console.log( 'CF7 form submitted successfully, showing success screen' );
                window.demoShowSuccess();
            }
        }, false );

        console.log( 'CF7 success handler initialized' );
    }

    /**
     * Initialize all page scripts when DOM is ready
     */
    function initPage() {
        initProgressBar();
        initSuccessHandler();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener( 'DOMContentLoaded', initPage );
    } else {
        // DOM already loaded
        initPage();
    }
})();
