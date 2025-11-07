/**
 * Animation Section - Lottie and Spline Animation on Scroll
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all animation elements with animation on scroll
    const lottieElements = document.querySelectorAll('lottie-player[data-animation-on-scroll]');
    const splineElements = document.querySelectorAll('spline-viewer[data-animation-on-scroll]');
    
    console.log('Found lottie elements:', lottieElements.length);
    console.log('Found spline elements:', splineElements.length);
    
    // Handle Lottie animations
    if (lottieElements.length > 0) {
        lottieElements.forEach(element => {
            // Track if animation has completed
            let animationCompleted = false;
            
            element.addEventListener('ready', () => {
                console.log('Lottie player ready:', element.id);
                
                // Set loop to false programmatically as backup
                element.loop = false;
                element.setLoop(false);
                
                // Also try setting it via the lottie instance
                if (element.getLottie) {
                    const lottieInstance = element.getLottie();
                    if (lottieInstance) {
                        lottieInstance.loop = false;
                        lottieInstance.setLoop(false);
                    }
                }
                
                // Listen for animation complete event
                element.addEventListener('complete', () => {
                    console.log('Lottie animation completed:', element.id);
                    animationCompleted = true;
                    // Ensure it stays at the last frame
                    element.stop();
                    // Force it to the last frame
                    element.seek('100%');
                });
            });
            
            element.addEventListener('error', (e) => {
                console.error('Lottie player error:', e);
            });
            
            // Store completion state on the element for later reference
            element.animationCompleted = () => animationCompleted;
        });
    }
    
    // Handle Spline animations
    if (splineElements.length > 0) {
        splineElements.forEach(element => {
            element.addEventListener('load', () => {
                console.log('Spline viewer loaded:', element.id);
            });
            
            element.addEventListener('error', (e) => {
                console.error('Spline viewer error:', e);
            });
        });
    }
    
    // Combine all animation elements
    const allAnimationElements = [...lottieElements, ...splineElements];
    
    if (allAnimationElements.length === 0) return;
    
    // Intersection Observer options
    const observerOptions = {
        root: null,
        rootMargin: '0px', // No margin adjustment
        threshold: 0.5 // Trigger when 50% (half) of element is visible
    };
    
    // Create intersection observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const animationElement = entry.target;
            const isLottie = animationElement.tagName.toLowerCase() === 'lottie-player';
            const isSpline = animationElement.tagName.toLowerCase() === 'spline-viewer';
            
            if (entry.isIntersecting) {
                console.log('Animation entering viewport:', animationElement.id);
                
                if (isLottie) {
                    // Handle Lottie animation
                    try {
                        // Check if animation has already completed
                        const hasCompleted = animationElement.animationCompleted && animationElement.animationCompleted();
                        
                        if (!hasCompleted) {
                            // Only play if not completed yet
                            if (animationElement.play) {
                                // Ensure loop is disabled before playing
                                animationElement.loop = false;
                                if (animationElement.setLoop) {
                                    animationElement.setLoop(false);
                                }
                                
                                animationElement.play();
                                console.log('Playing lottie animation (no loop)');
                            } else {
                                // Fallback if play method is not available
                                animationElement.setAttribute('autoplay', '');
                                console.log('Added autoplay attribute');
                            }
                        } else {
                            console.log('Lottie animation already completed, staying at end frame');
                            // Ensure it's stopped at the last frame
                            animationElement.stop();
                            animationElement.seek('100%');
                        }
                    } catch (error) {
                        console.error('Error playing lottie animation:', error);
                    }
                } else if (isSpline) {
                    // Handle Spline animation
                    try {
                        // Spline viewer automatically plays when visible
                        // We can trigger custom events or interactions here if needed
                        console.log('Spline animation is now visible');
                        
                        // Optional: Get the spline application and trigger events
                        if (animationElement.spline) {
                            // Custom spline interactions can be added here
                            console.log('Spline app available for interactions');
                        }
                    } catch (error) {
                        console.error('Error with spline animation:', error);
                    }
                }
                
            } else {
                console.log('Animation leaving viewport:', animationElement.id);
                
                if (isLottie) {
                    // Handle Lottie pause
                    try {
                        // Check if animation has completed
                        const hasCompleted = animationElement.animationCompleted && animationElement.animationCompleted();
                        
                        if (!hasCompleted) {
                            // Only pause if not completed - if completed, leave it at end frame
                            if (animationElement.pause) {
                                animationElement.pause();
                                console.log('Pausing lottie animation');
                            }
                        } else {
                            console.log('Lottie animation completed, keeping at end frame');
                            // Keep it stopped at the last frame
                            animationElement.stop();
                        }
                    } catch (error) {
                        console.error('Error pausing lottie animation:', error);
                    }
                } else if (isSpline) {
                    // Spline viewer doesn't need explicit pausing when out of view
                    // It handles performance automatically
                    console.log('Spline animation out of view (auto-optimized)');
                }
            }
        });
    }, observerOptions);
    
    // Observe all animation elements
    allAnimationElements.forEach(element => {
        observer.observe(element);
        console.log('Observing animation element:', element.id, '- Type:', element.tagName.toLowerCase());
    });
});