/**
 * Testimonials Slider - Splide.js Initialization
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all testimonials slider instances
    const sliderElements = document.querySelectorAll('.testimonials-slider__splide');
    
    if (sliderElements.length === 0) return;
    
    // Initialize each slider
    sliderElements.forEach(sliderElement => {
        // Get settings from data attributes
        const autoplay = sliderElement.getAttribute('data-autoplay') === 'true';
        const interval = parseInt(sliderElement.getAttribute('data-interval')) || 5000;
        const slidesPerView = parseFloat(sliderElement.getAttribute('data-slides-per-view')) || 2.5;
        
        // Create Splide instance
        const splide = new Splide(sliderElement, {
            type: 'loop',
            fixedWidth: '670px', // Fixed width for each slide
            perMove: 1,
            gap: '2rem',
            padding: { left: '0px', right: '0px' },
            autoplay: autoplay,
            interval: interval,
            pauseOnHover: true,
            pauseOnFocus: true,
            resetProgress: false,
            arrows: false,
            pagination: false,
            keyboard: true,
            drag: true,
            snap: true,
            flickPower: 600,
            flickMaxPages: 3,
            speed: 800,
            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
            breakpoints: {
                1200: {
                    fixedWidth: '670px', // Keep fixed width
                    gap: '1.5rem'
                },
                900: {
                    fixedWidth: '670px', // Keep fixed width
                    gap: '1rem'
                },
                768: {
                    fixedWidth: '500px', // Smaller fixed width for mobile
                    gap: '1rem'
                },
                480: {
                    perPage: 1, // Single slide on small mobile
                    gap: '2rem'
                }
            },
            classes: {
                arrow: 'splide__arrow',
                prev: 'splide__arrow--prev',
                next: 'splide__arrow--next',
                pagination: 'splide__pagination',
                page: 'splide__pagination__page'
            }
        });
        
        // Event listeners for better UX
        splide.on('mounted', function() {
            console.log('Testimonials slider mounted with', slidesPerView, 'slides per view');
            
            // Add focus management for accessibility
            const slides = sliderElement.querySelectorAll('.splide__slide');
            slides.forEach((slide, index) => {
                slide.setAttribute('role', 'tabpanel');
                slide.setAttribute('aria-label', `Testimonial ${index + 1}`);
            });
        });
        
        splide.on('moved', function(newIndex, prevIndex, destIndex) {
            // Optional: Track slide changes for analytics
            // console.log(`Slider moved from slide ${prevIndex} to slide ${newIndex}`);
        });
        
        splide.on('autoplay:playing', function(rate) {
            // Optional: Update progress indicator
            // console.log('Autoplay progress:', rate);
        });
        
        // Pause autoplay when user interacts
        splide.on('drag', function() {
            splide.Components.Autoplay.pause();
        });
        
        // Resume autoplay after user stops interacting
        let resumeTimer;
        splide.on('dragged', function() {
            clearTimeout(resumeTimer);
            resumeTimer = setTimeout(() => {
                if (autoplay) {
                    splide.Components.Autoplay.play();
                }
            }, 3000); // Resume after 3 seconds of inactivity
        });
        
        // Initialize the slider
        try {
            splide.mount();
            console.log('Testimonials slider initialized successfully with', slidesPerView, 'slides per view');
        } catch (error) {
            console.error('Error initializing testimonials slider:', error);
        }
        
        // Intersection Observer for performance (pause when not in view)
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Slider is in view - resume autoplay if enabled
                    if (autoplay && splide.Components.Autoplay) {
                        splide.Components.Autoplay.play();
                    }
                } else {
                    // Slider is out of view - pause autoplay
                    if (splide.Components.Autoplay) {
                        splide.Components.Autoplay.pause();
                    }
                }
            });
        }, observerOptions);
        
        observer.observe(sliderElement);
    });
});

// Utility function to handle dynamic content loading
function reinitializeTestimonialsSliders() {
    const event = new CustomEvent('DOMContentLoaded');
    document.dispatchEvent(event);
}

// Export for potential use in other scripts
if (typeof window !== 'undefined') {
    window.reinitializeTestimonialsSliders = reinitializeTestimonialsSliders;
}