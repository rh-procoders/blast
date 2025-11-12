document.addEventListener('DOMContentLoaded', function() {
    const leadershipBlocks = document.querySelectorAll('.leadership-block');
    
    leadershipBlocks.forEach(block => {
        const infoBtns = block.querySelectorAll('.leadership-member__info-btn');
        const closeBtns = block.querySelectorAll('.leadership-member__bio-close');
        const bioOverlays = block.querySelectorAll('.leadership-member__bio');
        
        // Handle info button clicks
        infoBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const memberIndex = this.getAttribute('data-member');
                const memberCard = this.closest('.leadership-member');
                const bioOverlay = memberCard.querySelector('.leadership-member__bio');
                
                // Close any other open bios
                bioOverlays.forEach(bio => {
                    if (bio !== bioOverlay) {
                        bio.classList.remove('is-open');
                        bio.closest('.leadership-member').classList.remove('bio-open');
                    }
                });
                
                // Toggle current bio
                if (bioOverlay) {
                    const isOpen = bioOverlay.classList.contains('is-open');
                    
                    if (isOpen) {
                        bioOverlay.classList.remove('is-open');
                        memberCard.classList.remove('bio-open');
                    } else {
                        bioOverlay.classList.add('is-open');
                        memberCard.classList.add('bio-open');
                        
                        // Scroll bio content to top
                        setTimeout(() => {
                            const bioContent = bioOverlay.querySelector('.leadership-member__bio-content');
                            if (bioContent) {
                                bioContent.scrollTop = 0;
                            }
                        }, 300);
                    }
                }
            });
        });
        
        // Handle close button clicks
        closeBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const memberIndex = this.getAttribute('data-close-bio');
                const bioOverlay = this.closest('.leadership-member__bio');
                const memberCard = this.closest('.leadership-member');
                
                if (bioOverlay && memberCard) {
                    bioOverlay.classList.remove('is-open');
                    memberCard.classList.remove('bio-open');
                }
            });
        });
        
        // Handle escape key to close bios
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                bioOverlays.forEach(bio => {
                    bio.classList.remove('is-open');
                    bio.closest('.leadership-member').classList.remove('bio-open');
                });
            }
        });
        
        // Handle clicks outside bio to close
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.leadership-member__bio') && 
                !e.target.closest('.leadership-member__info-btn')) {
                bioOverlays.forEach(bio => {
                    bio.classList.remove('is-open');
                    bio.closest('.leadership-member').classList.remove('bio-open');
                });
            }
        });
    });
});