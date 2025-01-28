document.getElementById('toggle-switch').addEventListener('change', function() {
    var equipmentsSection = document.getElementById('equipments-section');
    var facilitiesSection = document.getElementById('facilities-section');
    var switchLabel = document.getElementById('switch-label');
    if (this.checked) {
        equipmentsSection.style.display = 'none';
        facilitiesSection.style.display = 'block';
        switchLabel.textContent = 'Facilities';
    } else {
        equipmentsSection.style.display = 'block';
        facilitiesSection.style.display = 'none';
        switchLabel.textContent = 'Equipments';
    }
});
