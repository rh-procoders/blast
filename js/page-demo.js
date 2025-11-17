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
     * CF7 Button Error State Handler
     * Adds error class to buttons when validation fails
     */
    function initButtonErrorStates() {
        const form = document.querySelector( '.demo-section form.wpcf7-form' );

        if (!form) {
            console.log( 'CF7 form not found, skipping error state handler' );
            return;
        }

        /**
         * Add error class to the appropriate button
         */
        function addErrorClassToButton() {
            // Find the current active fieldset
            const currentFieldset = form.querySelector( '.fieldset-cf7mls.cf7mls_current_fs' );

            if (currentFieldset) {
                // Try to find Next button in current fieldset
                const nextButton = currentFieldset.querySelector( '.cf7mls_next' );
                if (nextButton && !nextButton.classList.contains( 'has-error' )) {
                    nextButton.classList.add( 'has-error' );
                    console.log( 'Error class added to Next button' );
                } else {
                    // No Next button found - must be final step with Submit button
                    const submitButton = form.querySelector( '.wpcf7-submit' );
                    if (submitButton && !submitButton.classList.contains( 'has-error' )) {
                        submitButton.classList.add( 'has-error' );
                        console.log( 'Error class added to Submit button' );
                    }
                }
            } else {
                // No current fieldset - fallback to Submit button
                const submitButton = form.querySelector( '.wpcf7-submit' );
                if (submitButton && !submitButton.classList.contains( 'has-error' )) {
                    submitButton.classList.add( 'has-error' );
                    console.log( 'Error class added to Submit button (fallback)' );
                }
            }
        }

        /**
         * Remove error class from all buttons
         */
        function removeErrorClassFromButtons() {
            const allButtons = form.querySelectorAll( '.cf7mls_next.has-error, .wpcf7-submit.has-error' );
            allButtons.forEach( button => {
                button.classList.remove( 'has-error' );
                console.log( 'Error class removed from button' );
            } );
        }

        // Method 1: Listen for CF7 validation error event
        document.addEventListener( 'wpcf7invalid', function ( event ) {
            if (event.target === form) {
                console.log( 'wpcf7invalid event fired' );
                addErrorClassToButton();
            }
        }, false );

        // Method 2: Watch for validation error message appearing (MutationObserver)
        const observer = new MutationObserver( function ( mutations ) {
            mutations.forEach( function ( mutation ) {
                // Check if validation errors div became visible
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const target = mutation.target;
                    if (target.classList.contains( 'wpcf7-validation-errors' )) {
                        const isVisible = target.style.display !== 'none' && target.style.display !== '';
                        if (isVisible) {
                            console.log( 'Validation errors became visible (MutationObserver)' );
                            addErrorClassToButton();
                        }
                    }
                }
            } );
        } );

        // Observe all validation error containers
        const errorContainers = form.querySelectorAll( '.wpcf7-response-output' );
        errorContainers.forEach( container => {
            observer.observe( container, {
                attributes: true,
                attributeFilter: ['style', 'class']
            } );
        } );

        // Method 3: Watch for "sending" class removal, then check for errors
        form.addEventListener( 'click', function ( event ) {
            const clickedButton = event.target.closest( '.cf7mls_next, .wpcf7-submit' );

            if (clickedButton) {
                console.log( 'Button clicked:', clickedButton.className );

                // Remove error class when user clicks (they're trying again)
                if (clickedButton.classList.contains( 'has-error' )) {
                    clickedButton.classList.remove( 'has-error' );
                }

                // Watch for when "sending" class is removed (validation complete)
                const buttonObserver = new MutationObserver( function ( mutations ) {
                    mutations.forEach( function ( mutation ) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            const button = mutation.target;

                            // Check if "sending" class was just removed
                            if (!button.classList.contains( 'sending' )) {
                                console.log( '"sending" class removed - validation complete, checking for errors...' );

                                // Small delay to ensure DOM is updated
                                setTimeout( function () {
                                    // Check 1: Look for visible error message
                                    const errorMessage = form.querySelector( '.wpcf7-response-output.wpcf7-validation-errors' );
                                    const hasVisibleErrorMessage = errorMessage && errorMessage.offsetParent !== null;

                                    // Check 2: Look for invalid field classes
                                    const hasInvalidFields = form.querySelector( '.wpcf7-not-valid, .cf7mls-invalid' ) !== null;

                                    // Check 3: Look for not-valid-tip spans
                                    const hasErrorTips = form.querySelector( '.wpcf7-not-valid-tip' ) !== null;

                                    console.log( 'Error message visible:', hasVisibleErrorMessage );
                                    console.log( 'Invalid fields present:', hasInvalidFields );
                                    console.log( 'Error tips present:', hasErrorTips );

                                    if (hasVisibleErrorMessage || hasInvalidFields || hasErrorTips) {
                                        console.log( 'Validation errors detected - adding error class to button' );
                                        addErrorClassToButton();
                                    } else {
                                        console.log( 'No validation errors detected - validation passed!' );
                                    }

                                    // Stop observing after check
                                    buttonObserver.disconnect();
                                }, 50 );
                            }
                        }
                    } );
                } );

                // Start observing the button for class changes
                buttonObserver.observe( clickedButton, {
                    attributes: true,
                    attributeFilter: ['class']
                } );
            }
        } );

        // Remove error class on successful form submission
        document.addEventListener( 'wpcf7mailsent', function ( event ) {
            if (event.target === form) {
                removeErrorClassFromButtons();
            }
        }, false );

        console.log( 'CF7 button error states initialized' );
    }

    /**
     * Initialize all page scripts when DOM is ready
     */
    function initPage() {
        initProgressBar();
        initSuccessHandler();
        initButtonErrorStates();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener( 'DOMContentLoaded', initPage );
    } else {
        // DOM already loaded
        initPage();
    }
})();
