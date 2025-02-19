document.addEventListener("DOMContentLoaded", function () {
    const gameDropdown = document.getElementById("gameDropdown");
    const playerStatsTable = document.querySelector(".player-stats tbody");

    // Event listener for dropdown change
    gameDropdown.addEventListener("change", function () {
        const gameID = this.value;

        if (gameID) {
            fetchPlayerStats(gameID);
        } else {
            playerStatsTable.innerHTML = "<tr><td colspan='19'>Select a game to view stats.</td></tr>";
        }
    });

    function fetchPlayerStats(gameID) {
        fetch(`fetch_player_statsf.php?gameID=${gameID}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    playerStatsTable.innerHTML = `<tr><td colspan='19'>${data.error}</td></tr>`;
                    return;
                }

                // Clear existing table content
                playerStatsTable.innerHTML = "";

                // Populate table with new data
                data.forEach(stat => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${stat.firstName} ${stat.lastName}</td>
                        <td>${stat.position}</td>
                        <td>${stat.points}</td>
                        <td>${stat.assists}</td>
                        <td>${stat.rebounds}</td>
                        <td>${stat.steals}</td>
                        <td>${stat.blocks}</td>
                        <td>${stat.turnovers}</td>
                        <td>${stat.minutesPlayed}</td>
                        <td>${stat.fieldGoalsMade}</td>
                        <td>${stat.fieldGoalsAttempted}</td>
                        <td>${isNaN(stat.fieldGoalsPercentage) ? "N/A" : Number(stat.fieldGoalsPercentage).toFixed(1) + "%"}</td>
                        <td>${stat.threePointersMade}</td>
                        <td>${stat.threePointersAttempted}</td>
                        <td>${isNaN(stat.threePointsPercentage) ? "N/A" : Number(stat.threePointsPercentage).toFixed(1) + "%"}</td>
                        <td>${stat.freeThrowsMade}</td>
                        <td>${stat.freeThrowsAttempted}</td>
                        <td>${isNaN(stat.freeThrowPercentage) ? "N/A" : Number(stat.freeThrowPercentage).toFixed(1) + "%"}</td>
                        <td>${stat.plusMinus}</td>
                    `;
                    playerStatsTable.appendChild(row);
                });

                if (data.length === 0) {
                    playerStatsTable.innerHTML = "<tr><td colspan='19'>No stats available for this game.</td></tr>";
                }
            })
            .catch(error => {
                console.error("Error fetching player stats:", error);
                playerStatsTable.innerHTML = "<tr><td colspan='19'>Error loading data.</td></tr>";
            });
    }
});

document.getElementById("gameDropdown").addEventListener("change", function() {
    var selectedOption = this.options[this.selectedIndex];
    var homeTeam = selectedOption.getAttribute("data-home");
    var awayTeam = selectedOption.getAttribute("data-away");
    var gameDate = selectedOption.getAttribute("data-date");

    if (homeTeam && awayTeam && gameDate) {
        document.getElementById("gameInfo").innerHTML = "GAME: " + homeTeam + " VS " + awayTeam + " &nbsp;&nbsp;&nbsp; DATE: " + gameDate;
    } else {
        document.getElementById("gameInfo").innerHTML = "GAME: - &nbsp;&nbsp;&nbsp; DATE: -";
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const gameDropdown = document.getElementById("gameDropdown");
    const gameInfo = document.getElementById("gameInfo");

    if (!gameDropdown || !gameInfo) {
        console.error("Dropdown or gameInfo element not found.");
        return;
    }

    gameDropdown.addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex].text;

        if (this.value) {
            gameInfo.innerHTML = selectedOption;
        } else {
            gameInfo.innerHTML = "GAME: - &nbsp;&nbsp;&nbsp; DATE: -";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".submit").addEventListener("click", function () {
        let gameID = document.getElementById("gameDropdown").value;
        let findings = document.getElementById("findings").value;
        let conclusion = document.getElementById("conclusion").value;
        let keyFindings = document.getElementById("keyFindings").value;

        if (!gameID) {
            alert("Please select a game.");
            return;
        }

        let formData = new FormData();
        formData.append("gameID", gameID);
        formData.append("findings", findings);
        formData.append("conclusion", conclusion);
        formData.append("keyFindings", keyFindings);

        fetch("save_analysis.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => console.error("Error:", error));
    });
});



