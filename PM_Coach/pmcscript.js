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

// Get modal elements
const addPlayerModal = document.getElementById("addPlayerModal");

// Open modal function
function openAddPlayerModal() {
    addPlayerModal.style.display = "block";
}

// Close modal function
function closeAddPlayerModal() {
    addPlayerModal.style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target === addPlayerModal) {
        closeAddPlayerModal();
    }
};

// Get modal elements
const injuryModal = document.getElementById("injuryModal");
const closeInjuryModalBtn = injuryModal.querySelector(".close");
const classifyInjuredBtn = document.querySelector(".classify-injured-btn");

// Open modal function
function openInjuryModal() {
    injuryModal.style.display = "block";
}

// Close modal function
function closeInjuryModal() {
    injuryModal.style.display = "none";
}

// Event listener for "CLASSIFY AS INJURED" button
classifyInjuredBtn.addEventListener("click", openInjuryModal);

// Event listener for close button
closeInjuryModalBtn.addEventListener("click", closeInjuryModal);

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target === injuryModal) {
        closeInjuryModal();
    }
};




