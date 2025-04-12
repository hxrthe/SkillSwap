document.addEventListener('DOMContentLoaded', function() {
    const toggleContainer = document.querySelector('.toggle-container');
    const toggleSlider = document.querySelector('.toggle-slider');
    const toggleButtons = document.querySelectorAll('.toggle-button');
    let activeButton = toggleButtons[0]; // Default to first button

    function updateSliderPosition(button) {
        const buttonRect = button.getBoundingClientRect();
        const containerRect = toggleContainer.getBoundingClientRect();
        
        // Set the width of the slider to match the button's text width plus padding
        toggleSlider.style.width = `${buttonRect.width - 10}px`; // Reduced padding subtraction
        
        // Calculate the left position relative to the container with offset
        const leftPosition = button.offsetLeft + 5; // Reduced offset to match container padding
        toggleSlider.style.left = `${leftPosition}px`;
        
        activeButton = button;
    }

    // Initialize slider position
    updateSliderPosition(activeButton);

    // Add click handlers to buttons
    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            updateSliderPosition(button);
        });
    });

    // Update on window resize
    window.addEventListener('resize', () => {
        updateSliderPosition(activeButton);
    });
}); 