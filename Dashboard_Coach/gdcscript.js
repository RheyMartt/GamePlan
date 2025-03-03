document.addEventListener("DOMContentLoaded", function () {
    // Dropdown functionality
    const dropdownButton = document.getElementById("dropdownButton");
    const dropdownContent = document.querySelector(".dropdown-content");

    if (dropdownButton && dropdownContent) {
        dropdownButton.addEventListener("click", function () {
            dropdownContent.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            if (!dropdownButton.contains(event.target) && !dropdownContent.contains(event.target)) {
                dropdownContent.classList.remove("show");
            }
        });

        document.querySelectorAll(".dropdown-content a").forEach((item) => {
            item.addEventListener("click", function () {
                dropdownButton.textContent = this.textContent;
            });
        });
    }

    // Adding game functionality
    const gameForm = document.querySelector("#addGameModal form");
    if (gameForm) {
        gameForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("addGame", "1");

            fetch("add_game.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.text())
                .then((data) => {
                    try {
                        const jsonData = JSON.parse(data);
                        alert(jsonData.message);
                        if (jsonData.status === "success") {
                            closeModal("addGameModal");
                            location.reload();
                        }
                    } catch (error) {
                        console.error("JSON Parsing Error:", error);
                        alert("An error occurred. Please check the console for details.");
                    }
                })
                .catch((error) => console.error("Fetch Error:", error));
        });
    }

    // Adding stats functionality
    const statsForm = document.querySelector("#addStatsModal form");
    if (statsForm) {
        statsForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            formData.append("addStats", "1");

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
    }

    // Close modal functionality
    document.querySelectorAll(".close").forEach((closeBtn) => {
        closeBtn.addEventListener("click", function () {
            const modal = this.closest(".modal");
            if (modal) closeModal(modal.id);
        });
    });

    // Open stats modal button
    const addStatsBtn = document.querySelector(".add-stats-btn");
    if (addStatsBtn) {
        addStatsBtn.addEventListener("click", function () {
            openModal("addStatsModal");
        });
    }

    // Game and player dropdown
    const gameDropdown = document.getElementById("gameID");
    const homePlayerDropdown = document.getElementById("homePlayerID");
    const awayPlayerDropdown = document.getElementById("awayPlayerID");

    // Fetch players for the selected game
    window.fetchPlayersForGame = function (gameID) {
        if (!gameID) {
            homePlayerDropdown.innerHTML = '<option value="">Select Player</option>';
            awayPlayerDropdown.innerHTML = '<option value="">Select Player</option>';
            return;
        }

        fetch(`fetch_players.php?gameID=${gameID}`)
            .then((response) => response.json())
            .then((data) => {
                homePlayerDropdown.innerHTML = '<option value="">Select Player</option>';
                data.homePlayers.forEach((player) => {
                    homePlayerDropdown.innerHTML += `<option value="${player.playerID}">${player.firstName} ${player.lastName}</option>`;
                });

                awayPlayerDropdown.innerHTML = '<option value="">Select Player</option>';
                data.awayPlayers.forEach((player) => {
                    awayPlayerDropdown.innerHTML += `<option value="${player.playerID}">${player.firstName} ${player.lastName}</option>`;
                });
            })
            .catch((error) => {
                console.error("Error fetching players:", error);
                alert("Failed to load players. Please try again.");
            });
    };

    // Define submitStatsForm globally
    window.submitStatsForm = function () {
        const formData = new FormData();
        formData.append("gameID", gameDropdown.value);
        formData.append("homePlayerID", homePlayerDropdown.value);
        formData.append("awayPlayerID", awayPlayerDropdown.value);
        formData.append("homePoints", document.getElementById("homePoints").value);
        formData.append("homeAssists", document.getElementById("homeAssists").value);
        formData.append("homeRebounds", document.getElementById("homeRebounds").value);
        formData.append("homeSteals", document.getElementById("homeSteals").value);
        formData.append("homeBlocks", document.getElementById("homeBlocks").value);
        formData.append("homeTurnovers", document.getElementById("homeTurnovers").value);
        formData.append("homeMinutesPlayed", document.getElementById("homeMinutesPlayed").value);
        formData.append("awayPoints", document.getElementById("awayPoints").value);
        formData.append("awayAssists", document.getElementById("awayAssists").value);
        formData.append("awayRebounds", document.getElementById("awayRebounds").value);
        formData.append("awaySteals", document.getElementById("awaySteals").value);
        formData.append("awayBlocks", document.getElementById("awayBlocks").value);
        formData.append("awayTurnovers", document.getElementById("awayTurnovers").value);
        formData.append("awayMinutesPlayed", document.getElementById("awayMinutesPlayed").value);

        fetch("insert_stats.php", {
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
            .catch((error) => {
                console.error("Error submitting stats:", error);
                alert("Failed to save stats. Please try again.");
            });
    };

    document.getElementById("csvUploadForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('upload_game_stats.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("uploadStatus").innerHTML = data.message;
        })
        .catch(error => {
            document.getElementById("uploadStatus").innerHTML = "Error uploading file.";
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const fileInput = document.querySelector("input[type='file']");
    const submitButton = document.querySelector("button[type='submit']");
    const messageDiv = document.createElement("div");

    form.appendChild(messageDiv);

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!fileInput.files.length) {
            messageDiv.innerHTML = "<p style='color: red;'>Please select a CSV file!</p>";
            return;
        }

        const formData = new FormData(form);
        submitButton.disabled = true;
        messageDiv.innerHTML = "<p style='color: blue;'>Uploading...</p>";

        fetch("upload_game_stats.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            messageDiv.innerHTML = `<p style='color: green;'>${data}</p>`;
            submitButton.disabled = false;
        })
        .catch(error => {
            messageDiv.innerHTML = "<p style='color: red;'>Upload failed. Try again.</p>";
            submitButton.disabled = false;
        });
    });
});


// Helper functions
function closeModal(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        modal.style.display = "none";
    } else {
        console.warn(`Modal with ID '${modalID}' not found.`);
    }
}

function openModal(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        modal.style.display = "block";
    } else {
        console.warn(`Modal with ID '${modalID}' not found.`);
    }
}




