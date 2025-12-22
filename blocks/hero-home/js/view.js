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
    
    // Handle click-to-play for local HTML5 videos with poster
    const playOverlay = block.querySelector('.hero-home__video-play-overlay');
    const htmlVideo = block.querySelector('.hero-home__html5-video');
    if (playOverlay && htmlVideo) {
        handleClickToPlay(playOverlay, htmlVideo);
    }
    
    // Handle click-to-play for embedded videos (YouTube/Vimeo)
    const embeddedOverlays = block.querySelectorAll('.hero-home__video-play-overlay[data-video-type]');
    embeddedOverlays.forEach(overlay => {
        handleClickToPlayEmbedded(overlay);
    });
    
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

function handleClickToPlay(overlay, videoElement) {
    const playButton = overlay.querySelector('.hero-home__play-button');
    
    if (playButton) {
        playButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Play the video
            videoElement.play();
            
            // Hide the overlay
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            overlay.style.transition = 'opacity 0.3s ease';
        });
    }
    
    // Hide overlay when video starts playing
    videoElement.addEventListener('play', function() {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
    });
    
    // Show overlay when video is paused (if it's at the beginning)
    videoElement.addEventListener('pause', function() {
        if (videoElement.currentTime === 0) {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        }
    });
}

function handleClickToPlayEmbedded(overlay) {
    const playButton = overlay.querySelector('.hero-home__play-button');
    const videoId = overlay.dataset.videoId;
    const videoType = overlay.dataset.videoType;
    const iframe = document.getElementById(videoId);
    
    if (!playButton || !iframe) return;
    
    // Create handler function
    function playVideo(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Show the iframe
        iframe.style.display = 'block';
        
        // Hide the overlay
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        
        // Trigger play based on video type
        if (videoType === 'youtube') {
            // YouTube: Reload iframe with autoplay parameter
            let currentSrc = iframe.getAttribute('src');
            
            if (currentSrc) {
                // Clean up existing parameters
                let newSrc = currentSrc.split('?')[0]; // Get base URL
                let params = new URLSearchParams(currentSrc.split('?')[1] || '');
                
                // Set autoplay and mute for YouTube
                params.set('autoplay', '1');
                params.set('mute', '1');
                
                // Rebuild URL
                newSrc = newSrc + '?' + params.toString();
                
                // Reset iframe src to trigger reload and autoplay
                iframe.src = '';
                setTimeout(() => {
                    iframe.src = newSrc;
                }, 50);
            }
        } else if (videoType === 'vimeo') {
            // Vimeo: Reload iframe with autoplay parameter
            let currentSrc = iframe.getAttribute('src');
            
            if (currentSrc) {
                // Clean up existing parameters
                let newSrc = currentSrc.split('?')[0]; // Get base URL
                let params = new URLSearchParams(currentSrc.split('?')[1] || '');
                
                // Set autoplay for Vimeo
                params.set('autoplay', '1');
                
                // Rebuild URL
                newSrc = newSrc + '?' + params.toString();
                
                // Reset iframe src to trigger reload and autoplay
                iframe.src = '';
                setTimeout(() => {
                    iframe.src = newSrc;
                }, 50);
            }
        }
    }
    
    // Add click handler to play button
    playButton.addEventListener('click', playVideo);
    playButton.addEventListener('touchend', playVideo);
    
    // Add click handler to overlay itself (for Safari compatibility - click anywhere on overlay)
    overlay.addEventListener('click', playVideo);
    overlay.addEventListener('touchend', playVideo);
    
    // Also handle direct clicks on the poster image
    const posterImg = overlay.querySelector('.hero-home__poster-image');
    if (posterImg) {
        posterImg.addEventListener('click', playVideo);
        posterImg.addEventListener('touchend', playVideo);
    }
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