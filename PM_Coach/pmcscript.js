document.addEventListener("DOMContentLoaded", function () {
    // Add event listener for player clicks
    const rosterItems = document.querySelectorAll(".player-link");

    rosterItems.forEach(item => {
        item.addEventListener("click", function () {
            // Get the playerID dynamically
            const playerID = this.getAttribute("data-playerid");
            console.log("PlayerID clicked:", playerID);

            // Make an AJAX request to fetch player data
            fetch("fetch_player.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error fetching bio:", data.error);
                    document.querySelector("#bio-section").innerHTML = "Player not found";
                } else {
                    // Update the bio section with player data
                    console.log("Fetched Player Bio:", data);
                    document.querySelector("#bio-section").innerHTML = `
                        <h3>Bio</h3>
                        <p>Name: ${data.firstName} ${data.lastName}</p>
                        <p>Position: ${data.position}</p>
                        <p>Status: ${data.status}</p>
                        <p>Height: ${data.height} | Weight: ${data.weight}</p>
                    `;
                }
            })
            .catch(error => console.error("Error fetching player bio:", error));

            // Make another AJAX request to fetch player stats
            fetch("fetch_player_stats.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error fetching stats:", data.error);
                } else {
                    document.getElementById('bio-section').innerHTML = `
                        <h3>Bio</h3>
                        <p>Name: ${data.firstName || 'N/A'} ${data.lastName || 'N/A'}</p>
                        <p>Position: ${data.position || 'N/A'}</p>
                        <p>Status: ${data.status || 'N/A'}</p>
                        <p>Height: ${data.height || 'N/A'} | Weight: ${data.weight || 'N/A'}</p>
                    `;
                    document.getElementById('stats-section').innerHTML = `
                        <h3>Stats</h3>
                        <p>PPG: ${data.ppg}</p>
                        <p>APG: ${data.apg}</p>
                        <p>RPG: ${data.rpg}</p>
                        <p>BPG: ${data.bpg}</p>
                        <p>SPG: ${data.spg}</p>
                    `;
                }
            })
            .catch(error => console.error("Error fetching player stats:", error));
        });
    });
});
