document.addEventListener('DOMContentLoaded', () => {
    // Functionality for "More From the Roster"
    const rosterItems = document.querySelectorAll('.roster-item');

    rosterItems.forEach(item => {
        item.addEventListener('click', () => {
            alert(`Redirecting to details for: ${item.querySelector('span:first-child').textContent}`);
        });
    });

    // Placeholder for additional JS logic (e.g., dynamic data loading, animations, etc.)
    console.log('Page fully loaded and scripts initialized.');
});
