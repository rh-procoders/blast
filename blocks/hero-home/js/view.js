/**
 * Hero Home Block Frontend JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize hero home blocks
    const heroBlocks = document.querySelectorAll('.hero-home');
    
    heroBlocks.forEach(function(block) {
        initHeroBlock(block);
    });
});

function initHeroBlock(block) {
    // Handle video loading and optimization
    const videoIframe = block.querySelector('.hero-home__video-iframe');
    if (videoIframe) {
        // Optimize video loading
        optimizeVideoLoading(videoIframe);
    }
    
    // Handle animation triggers if animations are enabled
    if (block.classList.contains('hero-home--animate')) {
        handleScrollAnimations(block);
    }
    
    // Handle responsive video sizing
    handleResponsiveVideo(block);
}

function optimizeVideoLoading(iframe) {
    // Add loading optimization
    iframe.setAttribute('loading', 'lazy');
    
    // Handle video error states
    iframe.addEventListener('error', function() {
        const placeholder = iframe.closest('.hero-home__video-wrapper');
        if (placeholder) {
            placeholder.innerHTML = `
                <div class="hero-home__video-error">
                    <p>Video could not be loaded</p>
                    <small>Please check the YouTube URL</small>
                </div>
            `;
        }
    });
}

function handleScrollAnimations(block) {
    // Only animate if block is in viewport
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('hero-home--in-view');
            }
        });
    }, {
        threshold: 0.1
    });
    
    observer.observe(block);
}

function handleResponsiveVideo(block) {
    // Handle responsive video sizing on window resize
    window.addEventListener('resize', function() {
        const videoContainer = block.querySelector('.hero-home__video-container');
        if (videoContainer) {
            // Maintain aspect ratio on resize
            updateVideoAspectRatio(videoContainer);
        }
    });
}

function updateVideoAspectRatio(container) {
    const wrapper = container.querySelector('.hero-home__video-wrapper, .hero-home__video-placeholder');
    if (wrapper) {
        // Force aspect ratio recalculation
        wrapper.style.paddingBottom = '56.25%';
    }
}

// Utility function to extract YouTube video ID
function getYouTubeVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

// Export for potential use in editor
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initHeroBlock,
        getYouTubeVideoId
    };
}