// Global Modal Functions
function openModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "block";
    } else {
        console.warn(`Modal with ID '${modalId}' not found.`);
    }
}

function closeModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    } else {
        console.warn(`Modal with ID '${modalId}' not found.`);
    }
}

// Close modal when clicking outside of it
window.onclick = function (event) {
    document.querySelectorAll(".modal").forEach((modal) => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
};

document.addEventListener("DOMContentLoaded", function () {
    const dropdownButton = document.getElementById("dropdownButton");
    const dropdownContent = document.querySelector(".dropdown-content");

    if (dropdownButton && dropdownContent) {
        // Toggle dropdown visibility
        dropdownButton.addEventListener("click", function (event) {
            dropdownContent.classList.toggle("show");
            event.stopPropagation();
        });

        // Prevent dropdown from closing when clicking inside
        dropdownContent.addEventListener("click", function (event) {
            event.stopPropagation();
        });

        // Close the dropdown when clicking outside
        document.addEventListener("click", function () {
            dropdownContent.classList.remove("show");
        });

        // Update button text based on selection
        document.querySelectorAll(".dropdown-content a").forEach((item) => {
            item.addEventListener("click", function (event) {
                event.preventDefault();
                dropdownButton.textContent = this.textContent.trim();
                dropdownContent.classList.remove("show");
            });
        });
    } else {
        console.error("Dropdown elements not found.");
    }

    // Adding game
    let gameForm = document.querySelector("#addGameModal form"); 
    if (gameForm) {
        gameForm.addEventListener("submit", function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            formData.append("addGame", "1"); // Ensures 'addGame' is always sent

            fetch("add_game.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.text()) // Get raw response
                .then((data) => {
                    console.log("Raw Response:", data); // Log before parsing
                    try {
                        let jsonData = JSON.parse(data); // Attempt to parse JSON
                        alert(jsonData.message);
                        if (jsonData.status === "success") {
                            closeModal("addGameModal");
                            location.reload();
                        }
                    } catch (error) {
                        console.error("JSON Parsing Error:", error);
                        console.log("Server response was not valid JSON:", data);
                        alert("An error occurred. Check the console for details.");
                    }
                })
                .catch((error) => console.error("Fetch Error:", error));
            
            
        });
    } else {
        console.error("Game form not found.");
    }

    // Adding stats
    let statsForm = document.querySelector("#addStatsModal form"); // Correct selector
    if (statsForm) {
        statsForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            formData.append("addStats", "1"); // Ensures 'addStats' is always sent

            fetch("add_stats.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    alert(data.message);
                    if (data.status === "success") {
                        closeModal("addStatsModal");
                        location.reload();
                    }
                })
                .catch((error) => console.error("Error:", error));
        });
    } else {
        console.warn("Stats form not found. Ensure #addStatsModal form exists in the HTML.");
    }

    // Assign modal functions to all close buttons
    document.querySelectorAll(".close").forEach((closeBtn) => {
        closeBtn.addEventListener("click", function () {
            let modal = this.closest(".modal");
            if (modal) closeModal(modal.id);
        });
    });

    // Button to open stats modal
    let addStatsBtn = document.querySelector(".add-stats-btn");
    if (addStatsBtn) {
        addStatsBtn.addEventListener("click", function () {
            openModal("addStatsModal");
        });
    }
});
