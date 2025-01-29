document.addEventListener('DOMContentLoaded', function() {
    const toggleSwitch = document.getElementById('toggle-switch');
    const switchLabel = document.getElementById('switch-label');
    const equipmentsSection = document.getElementById('equipments-section');
    const facilitiesSection = document.getElementById('facilities-section');

    // Get form label elements
    const borrowDateLabel = document.querySelector('label[for="borrow-date"]');
    const returnDateLabel = document.querySelector('label[for="return-date"]');
    const specificUseLabel = document.querySelector('label[for="specific-use"]');

    // Get input elements
    const borrowDateInput = document.getElementById('borrow-date');
    const returnDateInput = document.getElementById('return-date');
    const specificUseInput = document.getElementById('specific-use');

    toggleSwitch.addEventListener('change', function() {
        if (this.checked) {
            // Facilities view
            switchLabel.textContent = 'Facilities';
            equipmentsSection.style.display = 'none';
            facilitiesSection.style.display = 'block';
            
            // Change form labels and input types for Facilities
            borrowDateInput.type = 'date';
            returnDateInput.type = 'time';
            specificUseInput.type = 'time';
            
            // Clear input values when switching
            returnDateInput.value = '';
            specificUseInput.value = '';
            
            borrowDateLabel.textContent = 'Use Date:';
            returnDateLabel.textContent = 'Start Time:';
            specificUseLabel.textContent = 'End Time:';
        } else {
            // Equipment view
            switchLabel.textContent = 'Equipments';
            equipmentsSection.style.display = 'block';
            facilitiesSection.style.display = 'none';
            
            // Change form labels and input types back for Equipment
            borrowDateInput.type = 'date';
            returnDateInput.type = 'date';
            specificUseInput.type = 'text';
            
            // Clear input values when switching
            returnDateInput.value = '';
            specificUseInput.value = '';
            
            borrowDateLabel.textContent = 'Borrow Date:';
            returnDateLabel.textContent = 'Return Date:';
            specificUseLabel.textContent = 'Specific Use:';
        }
    });
});
