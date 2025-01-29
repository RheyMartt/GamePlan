document.addEventListener('DOMContentLoaded', function() {
    const toggleSwitch = document.getElementById('toggle-switch');
    const switchLabel = document.getElementById('switch-label');
    const equipmentsSection = document.getElementById('equipments-section');
    const facilitiesSection = document.getElementById('facilities-section');

    // Get form label elements
    const borrowDateLabel = document.querySelector('label[for="borrow-date"]');
    const returnDateLabel = document.querySelector('label[for="return-date"]');
    const specificUseLabel = document.querySelector('label[for="specific-use"]');

    toggleSwitch.addEventListener('change', function() {
        if (this.checked) {
            // Facilities view
            switchLabel.textContent = 'Facilities';
            equipmentsSection.style.display = 'none';
            facilitiesSection.style.display = 'block';
            
            // Change form labels for Facilities
            borrowDateLabel.textContent = 'Use Date:';
            returnDateLabel.textContent = 'Start Time:';
            specificUseLabel.textContent = 'End Time:';
        } else {
            // Equipment view
            switchLabel.textContent = 'Equipments';
            equipmentsSection.style.display = 'block';
            facilitiesSection.style.display = 'none';
            
            // Change form labels back for Equipment
            borrowDateLabel.textContent = 'Borrow Date:';
            returnDateLabel.textContent = 'Return Date:';
            specificUseLabel.textContent = 'Specific Use:';
        }
    });
});
