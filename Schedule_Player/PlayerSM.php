<?php
session_start(); // Start the session
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

// Ensure playerID is set in session
if (!isset($_SESSION['playerID'])) {
    echo "Player is not logged in!";
    exit;
}

$playerID = $_SESSION['playerID']; // Get the playerID from the session

function getUpcomingEvents($playerID) {
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
                                 WHERE t.trainingDate >= CURDATE() AND t.playerID = :playerID
                                 ORDER BY t.trainingDate ASC";

        $playerTrainingsStmt = $pdo->prepare($playerTrainingsQuery);
        $playerTrainingsStmt->execute(['playerID' => $playerID]);
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

// Fetch upcoming events by passing the playerID
$upcomingEvents = getUpcomingEvents($playerID);

// Function to fetch personal schedules for the logged-in player
function getPersonalSchedules($playerID) {
    global $pdo;

    try {
        // Fetch personal schedules
        $query = "SELECT schedDate, schedTime, type, notes
                  FROM personal_schedule
                  WHERE playerID = :playerID AND schedDate >= CURDATE()
                  ORDER BY schedDate ASC, schedTime ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['playerID' => $playerID]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $schedules;
    } catch (PDOException $e) {
        echo "Error fetching personal schedules: " . $e->getMessage();
        return [];
    }
}
$personalSchedules = getPersonalSchedules($playerID);



// Check if the required POST parameters are set
if (isset($_POST['type'], $_POST['schedDate'], $_POST['schedTime'])) {
    $type = $_POST['type'];
    $schedDate = $_POST['schedDate'];
    $schedTime = $_POST['schedTime'];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : 'n/a'; // Default to 'n/a' if not provided

    // Call the function to insert the schedule into the database
    include 'your_php_file_with_function.php'; // Include the PHP file with the addPersonalSchedule function
    $result = addPersonalSchedule($playerID, $type, $schedDate, $schedTime, $notes);

    if ($result) {
        echo "Schedule added successfully.";
    } else {
        echo "Error adding schedule.";
    }
} else {

}
// Function to insert a new schedule entry into the personal_schedule table
function addPersonalSchedule($playerID, $type, $schedDate, $schedTime, $notes) {
    global $pdo;

    try {
        $query = "INSERT INTO personal_schedule (playerID, type, schedDate, schedTime, notes)
                  VALUES (:playerID, :type, :schedDate, :schedTime, :notes)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'playerID' => $playerID,
            'type' => $type,
            'schedDate' => $schedDate,
            'schedTime' => $schedTime,
            'notes' => $notes
        ]);

        return true;
    } catch (PDOException $e) {
        echo "Error inserting schedule: " . $e->getMessage();
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU GAMEPLAN</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<style>
    .active {
        font-weight: bold;
    }
</style>
<body>
    <div class="container">
      <!-- Navigation Bar -->
      <div class="navbar">
        <div class="logo-container">
            <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
        </div>
        <div class="nav-links">
            <ul>
                <li><a href="/gameplan/Player_Profile_Player/Player.php" >PLAYER PROFILE</a></li>
                <li><a href="/gameplan/Schedule_Player/PlayerSM.html" class="active">SCHEDULE</a></li>
                <li><a href="/gameplan/PGM_Player/PGM.php">PROGRESS & MILESTONE</a></li>
                <li><a href="/gameplan/Login.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li> 
            </ul>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main">
        <!-- Left Panel -->
        <div class="panel">
            <h2>My Events</h2>
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
            <h2>My Calendar</h2>
            <div class="calendar-controls">
                <button id="prev-month">Previous</button>
                <span id="month-year"></span>
                <button id="next-month">Next</button>
            </div>
            <div id="calendar-container" class="calendar-grid"></div>
        </div>

        <!-- Right Panel -->
        <div class="panel schedule-panel">
            <h2>Personal Schedule</h2>
            <ul id="schedule-list"></ul>
        </div>
    </div>

    <script >
    const personalSchedules = <?php echo json_encode($personalSchedules); ?>;
    const eventDates = <?php echo json_encode(array_merge(
        array_column($upcomingEvents['games'], 'gameDate'),
        array_column($upcomingEvents['playerTrainings'], 'trainingDate'),
        array_column($upcomingEvents['teamTrainings'], 'trainingDate')
    )); ?>;
    </script>

    <script src="script.js"></script>
</body>
</html>