/**
 * Animation Section - Lottie Animation on Scroll
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all lottie players with animation on scroll
    const lottieElements = document.querySelectorAll('lottie-player[data-animation-on-scroll]');
    
    console.log('Found lottie elements:', lottieElements.length);
    
    if (lottieElements.length === 0) return;
    
    // Wait for lottie player to be ready
    lottieElements.forEach(element => {
        element.addEventListener('ready', () => {
            console.log('Lottie player ready:', element.id);
        });
        
        element.addEventListener('error', (e) => {
            console.error('Lottie player error:', e);
        });
    });
    
    // Intersection Observer options
    const observerOptions = {
        root: null,
        rootMargin: '0px', // No margin adjustment
        threshold: 0.5 // Trigger when 50% (half) of element is visible
    };
    
    // Create intersection observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const lottiePlayer = entry.target;
            
            if (entry.isIntersecting) {
                console.log('Lottie entering viewport:', lottiePlayer.id);
                // Element is in viewport - play animation
                try {
                    if (lottiePlayer.play) {
                        lottiePlayer.play();
                        console.log('Playing lottie animation');
                    } else {
                        // Fallback if play method is not available
                        lottiePlayer.setAttribute('autoplay', '');
                        console.log('Added autoplay attribute');
                    }
                } catch (error) {
                    console.error('Error playing lottie animation:', error);
                }
                
                // Optional: Stop observing after first play (remove if you want to replay on each scroll)
                // observer.unobserve(lottiePlayer);
            } else {
                console.log('Lottie leaving viewport:', lottiePlayer.id);
                // Element is out of viewport - pause animation
                try {
                    if (lottiePlayer.pause) {
                        lottiePlayer.pause();
                        console.log('Pausing lottie animation');
                    }
                } catch (error) {
                    console.error('Error pausing lottie animation:', error);
                }
            }
        });
    }, observerOptions);
    
    // Observe all lottie elements
    lottieElements.forEach(element => {
        observer.observe(element);
        console.log('Observing lottie element:', element.id);
    });
});