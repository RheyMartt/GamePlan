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
