document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".nav-links ul li a").forEach(link => {
      console.log("Found link:", link.textContent.trim()); // Debugging output
    });
  });
  

// Dropdown Functionality
function updateDropdown(game) {
  document.getElementById('dropdownButton').innerText = game;
}
