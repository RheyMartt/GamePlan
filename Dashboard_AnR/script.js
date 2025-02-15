document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".nav-links ul li a").forEach(link => {
    console.log("Found link:", link.textContent.trim()); // Debugging output
  });
});

function updateDropdown(game) {
    // Update the dropdown button text
    document.getElementById('dropdownButton').innerText = game;
  }
  function changeCourtImage(imageSrc) {
    document.getElementById('courtImage').src = imageSrc;
  }