<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

function getUpcomingEvents() {
    global $pdo;

    try {
        // Fetch upcoming games (today or future)
        $gamesQuery = "SELECT g.gameDate, g.gameTime, 
                              homeTeam.teamName AS homeTeam, 
                              awayTeam.teamName AS awayTeam, 
                              g.gameLocation, g.gameType
                       FROM games g
                       JOIN teams homeTeam ON g.homeTeamID = homeTeam.teamID
                       JOIN teams awayTeam ON g.awayTeamID = awayTeam.teamID
                       WHERE g.gameDate >= CURDATE() 
                       ORDER BY g.gameDate ASC";

        $gamesStmt = $pdo->prepare($gamesQuery);
        $gamesStmt->execute();
        $games = $gamesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch upcoming player trainings (today or future) with player names
        $playerTrainingsQuery = "SELECT t.trainingDate, t.trainingTime, 
                                        tp.focusArea AS trainingPlan, 
                                        p.firstName, p.lastName
                                 FROM training t
                                 JOIN trainingPlans tp ON t.trainingPlanID = tp.trainingPlanID
                                 JOIN players p ON t.playerID = p.playerID
                                 WHERE t.trainingDate >= CURDATE()
                                 ORDER BY t.trainingDate ASC";

        $playerTrainingsStmt = $pdo->prepare($playerTrainingsQuery);
        $playerTrainingsStmt->execute();
        $playerTrainings = $playerTrainingsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch upcoming team trainings (today or future)
        $teamTrainingsQuery = "SELECT tt.trainingDate, tt.trainingTime, 
                                      ttp.focusArea AS trainingPlan, 
                                      teams.teamName
                               FROM teamTraining tt
                               JOIN teamTrainingPlans ttp ON tt.teamTrainingPlanID = ttp.teamTrainingPlanID
                               JOIN teams ON tt.teamID = teams.teamID
                               WHERE tt.trainingDate >= CURDATE()
                               ORDER BY tt.trainingDate ASC";

        $teamTrainingsStmt = $pdo->prepare($teamTrainingsQuery);
        $teamTrainingsStmt->execute();
        $teamTrainings = $teamTrainingsStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'games' => $games,
            'playerTrainings' => $playerTrainings,
            'teamTrainings' => $teamTrainings
        ];

    } catch (PDOException $e) {
        echo "Error fetching upcoming events: " . $e->getMessage();
        return ['games' => [], 'playerTrainings' => [], 'teamTrainings' => []];
    }
}


// Fetch upcoming events
$upcomingEvents = getUpcomingEvents();


?>

<!-- File: index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU GAMEPLAN</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .active {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="logo-container">
                <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="/gameplan/Dashboard_Coach/GD.php">GAME DASHBOARD</a></li>
                    <li><a href="#">TEAM COMMUNICATION</a></li>
                    <li><a href="/gameplan/PM_Coach/PM.html">PLAYER MANAGEMENT</a></li>
                    <li><a href="#" class="active">SCHEDULE</a></li>
                    <li><a href="/gameplan/PGM_coach/PGM.php">PROGRESS & MILESTONE</a></li>
                    <li><a href="/gameplan/Resource_Management_Coach/RM.html">RESOURCES</a></li>
                    <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </div>
        </div>
      
       <!-- Main Content Section -->
            <div class="main">
                <!-- Left Panel -->
                <div class="panel">
                    <h2>Upcoming Events</h2>
                    <ul>
                        <?php
                        // Display upcoming games
                        foreach ($upcomingEvents['games'] as $game) {
                            echo "<li>Game: {$game['homeTeam']} vs {$game['awayTeam']} on {$game['gameDate']} at {$game['gameTime']} ({$game['gameLocation']})</li>";
                        }

                        // Display upcoming player trainings
                        foreach ($upcomingEvents['playerTrainings'] as $training) {
                            echo "<li>Player Training: {$training['firstName']} {$training['lastName']} - {$training['trainingPlan']} on {$training['trainingDate']} at {$training['trainingTime']}</li>";
                        }

                        // Display upcoming team trainings
                        foreach ($upcomingEvents['teamTrainings'] as $teamTraining) {
                            echo "<li>Team Training: {$teamTraining['teamName']} - {$teamTraining['trainingPlan']} on {$teamTraining['trainingDate']} at {$teamTraining['trainingTime']}</li>";
                        }
                        ?>
                    </ul>
                </div>
                <!-- Center Panel -->
                <div class="panel calendar-panel">
                    <h2>Calendar</h2>
                    <div class="calendar-controls">
                        <button id="prev-month">Previous</button>
                        <span id="month-year"></span>
                        <button id="next-month">Next</button>
                    </div>
                    <div id="calendar-container" class="calendar-grid"></div>
                </div>

                  <!-- Right Panel -->
                  <div class="panel schedule-panel">
                <h2>Add Game</h2>
                
                <label for="game-type">Game Type</label>
                <select id="game-type">
                    <option value="official">Official</option>
                    <option value="practice">Practice</option>
                </select>
                
                <label for="opponent">Opponent</label>
                <select id="opponent">
                    <option value="">Select Opponent</option>
                    <!-- Add opponent options dynamically -->
                </select>
                
                <label for="date">Date</label>
                <input type="date" id="date">
                
                <label for="time">Time</label>
                <input type="time" id="time">
                
                <label for="location">Location</label>
                <select id="location">
                    <option value="">Select Location</option>
                    <!-- Add location options dynamically -->
                </select>
                
                <button id="add-to-calendar">Add to Calendar</button>
            </div>
        </div>
    </div>

    <script>
    const eventDates = <?php echo json_encode(array_merge(
        array_column($upcomingEvents['games'], 'gameDate'),
        array_column($upcomingEvents['playerTrainings'], 'trainingDate'),
        array_column($upcomingEvents['teamTrainings'], 'trainingDate')
    )); ?>;
</script>
<script src="script.js"></script>

</body>
</html>
