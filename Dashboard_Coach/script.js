document.addEventListener("DOMContentLoaded", function () {
  const dropdownButton = document.getElementById("dropdownButton");
  const dropdownContent = document.querySelector(".dropdown-content");

  // Toggle dropdown visibility on button click
  dropdownButton.addEventListener("click", function () {
      dropdownContent.classList.toggle("show");
  });

  // Close the dropdown if clicked outside
  document.addEventListener("click", function (event) {
      if (!dropdownButton.contains(event.target) && !dropdownContent.contains(event.target)) {
          dropdownContent.classList.remove("show");
      }
  });

  // Automatically update the button text based on selection
  document.querySelectorAll(".dropdown-content a").forEach(item => {
      item.addEventListener("click", function () {
          dropdownButton.textContent = this.textContent;
      });
  });
});
