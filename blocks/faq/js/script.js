/**
 * FAQ Block - JavaScript
 */

(function() {
    'use strict';

    // Initialize FAQ accordion
    function initFAQ() {
        const faqItems = document.querySelectorAll('.faq-item-trigger');

        faqItems.forEach((trigger) => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();

                const faqItem = this.closest('.faq-item');
                const content = faqItem.querySelector('.faq-item-content');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                // Close all other items
                document.querySelectorAll('.faq-item').forEach((item) => {
                    if (item !== faqItem) {
                        const itemTrigger = item.querySelector('.faq-item-trigger');
                        const itemContent = item.querySelector('.faq-item-content');
                        itemTrigger.setAttribute('aria-expanded', 'false');
                        itemContent.removeAttribute('data-expanded');
                        itemContent.hidden = true;
                        item.classList.remove('active');
                    }
                });

                // Toggle current item
                if (isExpanded) {
                    this.setAttribute('aria-expanded', 'false');
                    content.removeAttribute('data-expanded');
                    content.hidden = true;
                    faqItem.classList.remove('active');
                } else {
                    this.setAttribute('aria-expanded', 'true');
                    content.setAttribute('data-expanded', 'true');
                    content.removeAttribute('hidden');
                    faqItem.classList.add('active');
                }
            });

            // Keyboard support - Enter and Space
            trigger.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
        initFAQ();
    }

})();
