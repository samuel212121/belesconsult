// ==========================================================================
// BELES CONSULTING P.L.C. - INTEGRATED CORE ENGINE
// Handles: Navigation Dropdowns, Sticky Header, Typewriter, and Hero Slider
// ==========================================================================

const menuToggleTrigger = document.getElementById("menuToggleTrigger");
const navLinksContainer = document.getElementById("navLinksContainer");
const individualNavLinks = document.querySelectorAll(".nav-link-item a");
const dropdownTriggers = document.querySelectorAll(".dropdown-trigger");

if (menuToggleTrigger && navLinksContainer) {
    menuToggleTrigger.addEventListener("click", (e) => {
        e.stopPropagation();
        menuToggleTrigger.classList.toggle("is-open");
        navLinksContainer.classList.toggle("is-active");
    });
}

dropdownTriggers.forEach(trigger => {
    trigger.addEventListener("click", function (e) {
        if (window.innerWidth <= 1024) {
            e.preventDefault(); 
            e.stopPropagation();

            const parentWrapper = this.parentElement;
            const associatedPanel = this.nextElementSibling;

            if (associatedPanel) {
                const isAlreadyOpen = associatedPanel.classList.contains("mobile-open");

                document.querySelectorAll(".dropdown-mega-panel").forEach(p => {
                    if (p !== associatedPanel) p.classList.remove("mobile-open");
                });
                document.querySelectorAll(".nav-link-item.dropdown-wrapper").forEach(w => {
                    if (w !== parentWrapper) w.classList.remove("chevron-active");
                });

                if (isAlreadyOpen) {
                    associatedPanel.classList.remove("mobile-open");
                    parentWrapper.classList.remove("chevron-active");
                } else {
                    associatedPanel.classList.add("mobile-open");
                    parentWrapper.classList.add("chevron-active");
                }
            }
        }
    });
});

individualNavLinks.forEach(link => {
    link.addEventListener("click", function(e) {
        // If the clicked link is a dropdown trigger, do not close the main slide-out nav drawer
        if (this.classList.contains("dropdown-trigger")) {
            return;
        }
        
        if (menuToggleTrigger && navLinksContainer) {
            menuToggleTrigger.classList.remove("is-open");
            navLinksContainer.classList.remove("is-active");
            
            document.querySelectorAll(".dropdown-mega-panel").forEach(p => p.classList.remove("mobile-open"));
            document.querySelectorAll(".nav-link-item.dropdown-wrapper").forEach(w => w.classList.remove("chevron-active"));
        }
    });
});

document.addEventListener("click", (e) => {
    if (window.innerWidth <= 1024) {
        if (navLinksContainer && navLinksContainer.classList.contains("is-active")) {
            if (!navLinksContainer.contains(e.target) && !menuToggleTrigger.contains(e.target)) {
                menuToggleTrigger.classList.remove("is-open");
                navLinksContainer.classList.remove("is-active");
                document.querySelectorAll(".dropdown-mega-panel").forEach(p => p.classList.remove("mobile-open"));
                document.querySelectorAll(".nav-link-item.dropdown-wrapper").forEach(w => w.classList.remove("chevron-active"));
            }
        }
    }
});


// --- 2. Scroll Delta Tracking Engine ---
const pageBody = document.body;
let lastTrackedScroll = 0;

window.addEventListener("scroll", () => {
    const verticalScrollOffset = window.pageYOffset;

    // Only run sticky scroll modifications on desktop screen widths
    if (window.innerWidth > 980) {
        if (verticalScrollOffset <= 90) {
            pageBody.classList.remove("scroll-down");
            pageBody.classList.remove("scrolled-past-top");
            return;
        }

        if (verticalScrollOffset > lastTrackedScroll && !pageBody.classList.contains("scroll-down")) {
            pageBody.classList.add("scroll-down");
            pageBody.classList.add("scrolled-past-top");
        } else if (verticalScrollOffset < lastTrackedScroll && pageBody.classList.contains("scroll-down")) {
            pageBody.classList.remove("scroll-down");
        }
    }
    lastTrackedScroll = verticalScrollOffset;
});


// --- 3. Typewriter Array Sequence Matrix ---
const servicesMatrix = ["Infrastructure Solutions.", "Consulting Engineering.", "Structural Designs."];
let currentArrayIdx = 0;
let characterPositionIdx = 0;
let internalDeleteActive = false;
const domTargetSpan = document.getElementById("typed-service-target");

function executeTypewriterRoutine() {
    if (!domTargetSpan) return;
    const continuousStringTarget = servicesMatrix[currentArrayIdx];
    
    if (internalDeleteActive) {
        domTargetSpan.textContent = continuousStringTarget.substring(0, characterPositionIdx - 1);
        characterPositionIdx--;
    } else {
        domTargetSpan.textContent = continuousStringTarget.substring(0, characterPositionIdx + 1);
        characterPositionIdx++;
    }

    let executionSpeedDelay = internalDeleteActive ? 40 : 85;

    if (!internalDeleteActive && characterPositionIdx === continuousStringTarget.length) {
        executionSpeedDelay = 2200;
        internalDeleteActive = true;
    } else if (internalDeleteActive && characterPositionIdx === 0) {
        internalDeleteActive = false;
        currentArrayIdx = (currentArrayIdx + 1) % servicesMatrix.length;
        executionSpeedDelay = 400;
    }

    setTimeout(executeTypewriterRoutine, executionSpeedDelay);
}


// --- 4. Hero Background Auto-Slider Engine ---
const heroSection = document.querySelector('.hero-viewport');
const leftArrow = document.getElementById('left-arrow');
const rightArrow = document.getElementById('right-arrow');

const sliderImages = [
    '/images/Silhouette Building construction site with cranes and skyscraper and  excavators with grader.jfif',
    '/images/project2.jpg', 
    '/images/project1.jpg'
];

let currentSlideIdx = 0;
let sliderTimer;

function updateHeroSlider(index) {
    if (!heroSection || sliderImages.length === 0) return;
    
    // Updates the CSS variable directly, triggering the smooth style transition crossfade
    heroSection.style.setProperty('--current-hero-bg', `url('${sliderImages[index]}')`);
}

function nextSlide() {
    currentSlideIdx = (currentSlideIdx + 1) % sliderImages.length;
    updateHeroSlider(currentSlideIdx);
}

function prevSlide() {
    currentSlideIdx = (currentSlideIdx - 1 + sliderImages.length) % sliderImages.length;
    updateHeroSlider(currentSlideIdx);
}

function resetSliderTimer() {
    clearInterval(sliderTimer);
    sliderTimer = setInterval(nextSlide, 6000); // Transitions automatically every 6 seconds
}

// Action listeners mapping direct button interactions to slider controls
if (rightArrow && leftArrow) {
    rightArrow.addEventListener('click', () => {
        nextSlide();
        resetSliderTimer();
    });

    leftArrow.addEventListener('click', () => {
        prevSlide();
        resetSliderTimer();
    });
}


// --- 5. Global Document Lifecycle Core Initialization ---
document.addEventListener("DOMContentLoaded", () => {
    // Fire up Typewriter Engine
    setTimeout(executeTypewriterRoutine, 500);
    
    // Fire up Background Slider Engine
    updateHeroSlider(currentSlideIdx);
    resetSliderTimer();
});