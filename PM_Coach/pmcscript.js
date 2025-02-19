document.addEventListener("DOMContentLoaded", function () {
    // Add event listener for player clicks
    const rosterItems = document.querySelectorAll(".player-link");

    rosterItems.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link behavior

            // Get the playerID dynamically
            const playerID = this.getAttribute("data-playerid");
            console.log("PlayerID clicked:", playerID);

            // Fetch player bio
            fetch("fetch_player.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching bio:", data.error);
                        document.querySelector("#bio-section").innerHTML = "<p>Player not found</p>";
                    } else {
                        // Dynamically update the bio section with player data
                        document.querySelector("#bio-section").innerHTML = `
                            <div class="section">
                                <h3>Bio</h3>
                                <p><strong>Name:</strong> ${data.firstName} ${data.lastName}</p>
                                <p><strong>Position:</strong> ${data.position}</p>
                                <p><strong>Status:</strong> ${data.status}</p>
                                <p><strong>Height & Weight:</strong> ${data.height} | ${data.weight}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error("Error fetching player bio:", error));

            // Fetch player stats
            fetch("fetch_player_stats.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
                .then(response => response.json()) // Parse JSON response
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching stats:", data.error);
                        document.querySelector("#stats-section").innerHTML = `
                            <div class="section error-message">${data.error}</div>
                        `;
                    } else {
                        // Dynamically generate the stats content with a table
                        document.querySelector("#stats-section").innerHTML = `
                            <div class="section">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Stat</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Points Per Game (PPG)</td>
                                            <td>${data.ppg}</td>
                                        </tr>
                                        <tr>
                                            <td>Assists Per Game (APG)</td>
                                            <td>${data.apg}</td>
                                        </tr>
                                        <tr>
                                            <td>Rebounds Per Game (RPG)</td>
                                            <td>${data.rpg}</td>
                                        </tr>
                                        <tr>
                                            <td>Blocks Per Game (BPG)</td>
                                            <td>${data.bpg}</td>
                                        </tr>
                                        <tr>
                                            <td>Steals Per Game (SPG)</td>
                                            <td>${data.spg}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error("Error fetching player stats:", error));
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Add event listener for player clicks
    const rosterItems = document.querySelectorAll(".player-link");

    rosterItems.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link behavior

            // Get the playerID dynamically
            const playerID = this.getAttribute("data-playerid");
            console.log("Fetching attendance for Player ID:", playerID);

            // Fetch attendance data
            fetch("fetch_attendance.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching attendance:", data.error);
                        document.querySelector("#attendance-section").innerHTML = `
                            <div class="section error-message">${data.error}</div>
                        `;
                    } else {
                        // Dynamically update the attendance section with player attendance data
                        document.querySelector("#attendance-section").innerHTML = `
                            <div class="section">
                                <h3>Attendance</h3>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total Sessions</td>
                                            <td>${data.total_sessions}</td>
                                        </tr>
                                        <tr>
                                            <td>Attended</td>
                                            <td>${data.attended_sessions}</td>
                                        </tr>
                                        <tr>
                                            <td>Missed</td>
                                            <td>${data.missed_sessions}</td>
                                        </tr>
                                        <tr>
                                            <td>Last Attendance</td>
                                            <td>${data.lastAttendanceDate}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error("Error fetching attendance:", error));
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const addPlayerBtn = document.querySelector(".add-player-btn");
    const addPlayerModal = document.getElementById("addPlayerModal");
    const closeButtons = document.querySelectorAll(".close");
    const addPlayerForm = document.getElementById("addPlayerForm");

    // Show modal when "Add Player" button is clicked
    addPlayerBtn.addEventListener("click", function () {
        addPlayerModal.style.display = "block";
    });

    // Close modal when 'x' is clicked
    closeButtons.forEach(button => {
        button.addEventListener("click", function () {
            addPlayerModal.style.display = "none";
        });
    });

    // Close modal when clicking outside of modal
    window.addEventListener("click", function (event) {
        if (event.target === addPlayerModal) {
            addPlayerModal.style.display = "none";
        }
    });

    // Handle form submission via AJAX
    addPlayerForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(addPlayerForm);

        fetch("add_player.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Player added successfully!");
                addPlayerModal.style.display = "none";
                addPlayerForm.reset();
                location.reload(); // Refresh to update the roster
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});


//Injured Modal
document.addEventListener("DOMContentLoaded", function () {
    let selectedPlayerID = null; // Declare globally

    const classifyInjuredBtn = document.getElementById("classifyInjuredBtn");
    const confirmInjuryBtn = document.getElementById("confirmInjuryBtn");
    const injuryModal = document.getElementById("injuryModal");
    const revertActiveBtn = document.getElementById("revertActiveBtn");
    const closeBtns = document.querySelectorAll(".close");

    // Open modal when "Classify as Injured" is clicked
    classifyInjuredBtn.addEventListener("click", function () {
        if (!selectedPlayerID) {
            alert("Please select a player first.");
            return;
        }
        injuryModal.style.display = "block";
    });

    // Close modal
    closeBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            injuryModal.style.display = "none";
        });
    });

    // Confirm Injury
    confirmInjuryBtn.addEventListener("click", function () {
        const injuryType = document.getElementById("injuryType").value;
        const injuryDate = document.getElementById("injuryDate").value;

        if (!injuryType || !injuryDate) {
            alert("Please fill in all fields.");
            return;
        }

        fetch("classify_injured.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `playerID=${selectedPlayerID}&injuryType=${injuryType}&injuryDate=${injuryDate}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Player classified as injured.");
                injuryModal.style.display = "none";
                classifyInjuredBtn.style.display = "none";
                revertActiveBtn.style.display = "inline-block";
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Handle player selection (assuming you have player buttons with data-playerid)
    document.querySelectorAll(".player-link").forEach(player => {
        player.addEventListener("click", function () {
            selectedPlayerID = this.getAttribute("data-playerid");
            checkPlayerStatus(selectedPlayerID);
        });
    });

    // Function to check if player is injured
    function checkPlayerStatus(playerID) {
        fetch(`get_player_status.php?playerID=${playerID}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "Injured") {
                classifyInjuredBtn.style.display = "none";
                revertActiveBtn.style.display = "inline-block";
            } else {
                classifyInjuredBtn.style.display = "inline-block";
                revertActiveBtn.style.display = "none";
            }
        });
    }

    // Handle "Revert to Active" button
    revertActiveBtn.addEventListener("click", function () {
        if (!selectedPlayerID) {
            alert("Please select a player first.");
            return;
        }

        fetch("revert_active.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `playerID=${selectedPlayerID}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Player status reverted to active.");
                revertActiveBtn.style.display = "none";
                classifyInjuredBtn.style.display = "inline-block";
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});



//Remove player
    document.addEventListener("DOMContentLoaded", function () {
        let selectedPlayerID = null;

    // Listen for clicks on player links
    document.querySelectorAll(".player-link").forEach(link => {
        link.addEventListener("click", function () {
            selectedPlayerID = this.getAttribute("data-playerid");
            document.getElementById("removePlayerBtn").disabled = false;
        });
    });

    

    // Handle remove button click
    document.getElementById("removePlayerBtn").addEventListener("click", function () {
        if (!selectedPlayerID) {
            alert("Please select a player first!");
            return;
        }

        if (confirm("Are you sure you want to remove this player?")) {
            fetch("remove_player.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + selectedPlayerID
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Player removed successfully!");
                    location.reload(); // Refresh the page to update the roster
                } else {
                    alert("Error: " + data);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});





