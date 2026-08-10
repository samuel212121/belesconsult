// --- Interactive Animation Controller Engine for About Sub-Modules ---

document.addEventListener("DOMContentLoaded", () => {
    initializeWaveRippleEngine();
    initializeFormSubmissionMock();
});

/**
 * 1. RADIAL SHOCKWAVE INTERACTION ENGINE
 * Dynamically tracks cursor hit targets inside structural button footprints 
 * and handles the generation of spreading vector visual layers.
 */
function initializeWaveRippleEngine() {
    const waveTriggerButtons = document.querySelectorAll(".wave-ripple-btn");

    waveTriggerButtons.forEach(button => {
        button.addEventListener("click", function(e) {
            // Track relative positional contact entries inside boundaries
            const clientBoundaryRect = this.getBoundingClientRect();
            const clickCoordinatesX = e.clientX - clientBoundaryRect.left;
            const clickCoordinatesY = e.clientY - clientBoundaryRect.top;

            // Compute maximum dimensional requirements for shockwave radius canvas coverage
            const maximumBtnDimension = Math.max(clientBoundaryRect.width, clientBoundaryRect.height);

            // Construct structural shockwave layer node
            const rippleNode = document.createElement("span");
            rippleNode.classList.add("ripple-shockwave-node");

            // Direct measurement injection sizing parameters mapping target area layout
            rippleNode.style.width = rippleNode.style.height = `${maximumBtnDimension * 2}px`;
            rippleNode.style.left = `${clickCoordinatesX - maximumBtnDimension}px`;
            rippleNode.style.top = `${clickCoordinatesY - maximumBtnDimension}px`;

            // Purge legacy active tracking nodes to optimize structural layout memory allocations
            const structuralLegacyNode = this.querySelector(".ripple-shockwave-node");
            if (structuralLegacyNode) {
                structuralLegacyNode.remove();
            }

            // Append vector element layer to DOM tree context
            this.appendChild(rippleNode);
        });
    });
}

/**
 * 2. NEWSLETTER FORM VALIDATION MOCK ROUTINE
 * Protects layout stability and alerts user context on verification completion.
 */
function initializeFormSubmissionMock() {
    const newsletterForm = document.getElementById("newsletterForm");
    
    if (newsletterForm) {
        newsletterForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector("input[type='email']");
            
            if (emailInput && emailInput.value.trim() !== "") {
                alert(`Thank you. An engineering advisor from Beles Consulting will connect with ${emailInput.value.trim()} shortly.`);
                emailInput.value = "";
            }
        });
    }
}