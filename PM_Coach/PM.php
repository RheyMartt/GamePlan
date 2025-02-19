<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath

// Default playerID (can be modified to select a specific player based on URL parameters)
$playerID = isset($_GET['playerID']) ? $_GET['playerID'] : 1;

// Default values for stats
$gamesPlayed = 0;
$totalPoints = $totalAssists = $totalRebounds = $totalBlocks = $totalSteals = 0;
$ppg = $apg = $rpg = $bpg = $spg = 0;

// Fetch player data
try {
    $stmt = $pdo->prepare("SELECT firstName, lastName, position, status, height, weight FROM players WHERE playerID = :playerID");
    $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
    $stmt->execute();
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch player stats
try {
    $stmt = $pdo->prepare("SELECT 
        COUNT(DISTINCT gameID) AS games_played, 
        COALESCE(SUM(points), 0) AS total_points, 
        COALESCE(SUM(assists), 0) AS total_assists, 
        COALESCE(SUM(rebounds), 0) AS total_rebounds, 
        COALESCE(SUM(blocks), 0) AS total_blocks, 
        COALESCE(SUM(steals), 0) AS total_steals 
        FROM game_stats 
        WHERE playerID = :playerID");
    $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stats) {
        $gamesPlayed = (int) $stats['games_played'];
        $totalPoints = (int) $stats['total_points'];
        $totalAssists = (int) $stats['total_assists'];
        $totalRebounds = (int) $stats['total_rebounds'];
        $totalBlocks = (int) $stats['total_blocks'];
        $totalSteals = (int) $stats['total_steals'];

        if ($gamesPlayed > 0) {
            $ppg = $totalPoints / $gamesPlayed;
            $apg = $totalAssists / $gamesPlayed;
            $rpg = $totalRebounds / $gamesPlayed;
            $bpg = $totalBlocks / $gamesPlayed;
            $spg = $totalSteals / $gamesPlayed;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch attendance stats for the player
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_sessions,
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS attended_sessions,
            SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS missed_sessions,
            MAX(sessionID) AS last_attendance_session
        FROM attendance 
        WHERE playerID = :playerID
    ");
    $stmt->execute(['playerID' => $playerID]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    // Initialize attendance values with 0 (to prevent undefined variable errors)
    $totalSessions = $attendance['total_sessions'] ?? 0;
    $attendedSessions = $attendance['attended_sessions'] ?? 0;
    $missedSessions = $attendance['missed_sessions'] ?? 0;
    $lastAttendanceDate = "No record";  // Default value

    // Fetch last attendance date
    if (!empty($attendance['last_attendance_session'])) {
        $stmt = $pdo->prepare("SELECT sessionType, sessionID FROM attendance WHERE sessionID = :sessionID");
        $stmt->execute(['sessionID' => $attendance['last_attendance_session']]);
        $lastSession = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastSession) {
            $lastAttendanceDate = "Session " . $lastSession['sessionID'] . " (" . $lastSession['sessionType'] . ")";
        }
    }
} catch (PDOException $e) {
    echo "Error fetching attendance data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU GAMEPLAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pmcstyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="player-management">
        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="logo-container">
                <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="/gameplan/Dashboard_Coach/GD.php">GAME DASHBOARD</a></li>
                    <li><a href="#">TEAM COMMUNICATION</a></li>
                    <li><a href="#" class="active">PLAYER MANAGEMENT</a></li>
                    <li><a href="/gameplan/Schedule_Coach/SM.html">SCHEDULE</a></li>
                    <li><a href="/gameplan/PGM_coach/PGM.html">PROGRESS & MILESTONE</a></li>
                    <li><a href="/gameplan/Resource_Management_Coach/RM.html">EQUIPMENTS</a></li>
                    <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="add-player-container">
            <button class="add-player-btn" onclick="openAddPlayerModal()">Add Player</button>
        </div>
  
        <!-- More From the Roster Section -->
        <section class="roster">
            <div class="roster-list">
                <?php
                // Fetch only players with teamID = 1
                $stmt = $pdo->prepare("SELECT playerID, firstName, lastName FROM players WHERE teamID = :teamID");
                $stmt->execute(['teamID' => 1]); 
                $players = $stmt->fetchAll();

                foreach ($players as $player) {
                    echo '<div class="roster-item">';
                    echo '<a href="javascript:void(0);" class="player-link" data-playerid="' . $player['playerID'] . '">';
                    echo '<img src="Player.png" alt="Player" class="player-btn">';
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- New Container with 3 Sections, stacked vertically -->
            <div class="container">
                <!-- Section 1: Bio -->
                <div id="bio-section" class="section">
                    <h3>Bio</h3>
                    <p>Click on a player to view their bio.</p>
                </div>

                <!-- Section 2: Stats -->
                <div id="stats-section" class="section">
                    <p>No stats available. Click on a player to view stats.</p>
                </div>

                <!-- Section 3: Attendance -->
                <div id="attendance-section" class="section">
                    <h3>Attendance</h3>
                    <p>Click on a player to view attendance.</p>
                </div>

                <div class="buttons-container">
                    <button id="classifyInjuredBtn" class="classify-injured-btn">CLASSIFY AS INJURED</button>
                    <button class="remove-player-btn">REMOVE</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Player Modal -->
    <div id="addPlayerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddPlayerModal()">&times;</span>
            <h2>Add New Player</h2>
            <form id="addPlayerForm">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>

                <label for="position">Position:</label>
                <select id="position" name="position">
                    <option value="Guard">Guard</option>
                    <option value="Forward">Forward</option>
                    <option value="Center">Center</option>
                </select>

                <label for="height">Height (cm):</label>
                <input type="number" id="height" name="height" required>

                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" required>

                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Active">Active</option>
                    <option value="Injured">Injured</option>
                </select>

                <button type="submit">Add Player</button>
            </form>
        </div>
    </div>


    <!-- Injury Modal -->
    <div id="injuryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Classify as Injured</h2>
            <p>Are you sure you want to classify this player as injured?</p>
            <label for="injuryType">Injury Type:</label>
            <input type="text" id="injuryType" name="injuryType" placeholder="Enter injury type">
            <label for="injuryDate">Date Injured:</label>
            <input type="date" id="injuryDate" name="injuryDate">
            <button id="confirmInjuryBtn">Confirm</button>
        </div>
    </div>

    <!-- Include the external JavaScript -->
    <script src="pmcscript.js"></script>
</body>
</html>
