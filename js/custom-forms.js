/**
 * Custom Forms Scripts
 *
 * Collection of form-related JavaScript enhancements
 */

(function () {
    'use strict';

    /**
     * Custom 2px Wide Caret
     * Replaces the default 1px browser caret with a 2px wide version
     */
    class CustomCaret {
        constructor( input ) {
            this.input = input;
            this.caretElement = null;
            this.isActive = false;

            this.init();
        }

        init() {
            // Mark input as initialized
            this.input.classList.add( 'has-custom-caret' );

            // Create caret element with "|" character
            this.caretElement = document.createElement( 'div' );
            this.caretElement.className = 'custom-caret';
            this.caretElement.textContent = '|'; // Use "|" character as caret
            this.caretElement.style.display = 'none'; // Hidden by default until focus

            // Insert caret after input (inside wrapper)
            this.input.parentNode.insertBefore( this.caretElement, this.input.nextSibling );

            // Bind events
            this.input.addEventListener( 'focus', () => this.show() );
            this.input.addEventListener( 'blur', () => this.hide() );

            // Use requestAnimationFrame to ensure DOM updates before repositioning
            this.input.addEventListener( 'input', () => {
                requestAnimationFrame( () => this.updatePosition() );
            } );
            this.input.addEventListener( 'click', () => {
                requestAnimationFrame( () => this.updatePosition() );
            } );
            this.input.addEventListener( 'keyup', () => {
                requestAnimationFrame( () => this.updatePosition() );
            } );
            this.input.addEventListener( 'keydown', ( e ) => {
                // Update position after key is processed
                requestAnimationFrame( () => this.updatePosition() );
            } );

            // Handle text selection
            this.input.addEventListener( 'select', () => {
                if (this.input.selectionStart !== this.input.selectionEnd) {
                    this.hide();
                } else {
                    requestAnimationFrame( () => this.updatePosition() );
                }
            } );
        }

        show() {
            this.isActive = true;
            this.updatePosition();
            this.caretElement.style.display = 'block';
        }

        hide() {
            this.isActive = false;
            this.caretElement.style.display = 'none';
        }

        updatePosition() {
            if (!this.isActive) return;

            // Get cursor position - fallback to end of text if selectionStart is null
            let cursorPos = this.input.selectionStart;
            const inputValue = this.input.value;

            // Fallback: if selectionStart is null, assume cursor is at the end
            if (cursorPos === null || cursorPos === undefined) {
                cursorPos = inputValue.length;
            }

            // Get input styles
            const inputStyles = window.getComputedStyle( this.input );
            const paddingLeft = parseFloat( inputStyles.paddingLeft );
            const paddingTop = parseFloat( inputStyles.paddingTop );
            const borderLeft = parseFloat( inputStyles.borderLeftWidth ) || 0;
            const borderTop = parseFloat( inputStyles.borderTopWidth ) || 0;

            // Create a temporary element to measure text width
            const measureElement = document.createElement( 'span' );
            measureElement.style.cssText = `
                position: absolute;
                visibility: hidden;
                white-space: pre;
                font-family: ${inputStyles.fontFamily};
                font-size: ${inputStyles.fontSize};
                font-weight: ${inputStyles.fontWeight};
                letter-spacing: ${inputStyles.letterSpacing};
                line-height: ${inputStyles.lineHeight};
            `;
            measureElement.textContent = inputValue.substring( 0, cursorPos );

            document.body.appendChild( measureElement );
            const textWidth = measureElement.offsetWidth;
            document.body.removeChild( measureElement );

            // Get positions relative to wrapper
            const inputRect = this.input.getBoundingClientRect();
            const wrapperRect = this.input.parentNode.getBoundingClientRect();
            const scrollLeft = this.input.scrollLeft;

            // Calculate caret position RELATIVE to wrapper (not viewport)
            const caretLeft = (inputRect.left - wrapperRect.left) + borderLeft + paddingLeft + textWidth - scrollLeft;
            const caretTop = (inputRect.top - wrapperRect.top) + borderTop + paddingTop;

            // Update caret element position (height is determined by "I" character naturally)
            this.caretElement.style.left = caretLeft + 'px';
            this.caretElement.style.top = caretTop + 'px';
        }

        destroy() {
            this.hide();
            this.input.classList.remove( 'has-custom-caret' );
            if (this.caretElement && this.caretElement.parentNode) {
                this.caretElement.parentNode.removeChild( this.caretElement );
            }
        }
    }

    /**
     * Initialize custom carets for all text inputs
     */
    function initCustomCarets() {
        const textInputs = document.querySelectorAll(
            'input[type="text"], ' +
            'input[type="email"], ' +
            'input[type="url"], ' +
            'input[type="password"], ' +
            'input[type="search"], ' +
            'input[type="tel"]'
        );

        textInputs.forEach( input => {
            // Check if already initialized
            if (!input.classList.contains( 'has-custom-caret' )) {
                new CustomCaret( input );
            }
        } );
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener( 'DOMContentLoaded', initCustomCarets );
    } else {
        initCustomCarets();
    }

    /**
     * Re-initialize for dynamically added inputs (AJAX forms, etc.)
     */
    const observer = new MutationObserver( mutations => {
        mutations.forEach( mutation => {
            mutation.addedNodes.forEach( node => {
                if (node.nodeType === 1) { // Element node
                    if (node.matches && node.matches( 'input[type="text"], input[type="email"], input[type="url"], input[type="password"], input[type="search"], input[type="tel"]' )) {
                        if (!node.classList.contains( 'has-custom-caret' )) {
                            new CustomCaret( node );
                        }
                    } else if (node.querySelectorAll) {
                        const inputs = node.querySelectorAll( 'input[type="text"], input[type="email"], input[type="url"], input[type="password"], input[type="search"], input[type="tel"]' );
                        inputs.forEach( input => {
                            if (!input.classList.contains( 'has-custom-caret' )) {
                                new CustomCaret( input );
                            }
                        } );
                    }
                }
            } );
        } );
    } );

    observer.observe( document.body, {
        childList: true,
        subtree: true
    } );

})();
