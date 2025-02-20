<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath
session_start(); // Start the session

// Check if playerID is set in the session
if (!isset($_SESSION['playerID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Get playerID from session
$playerID = $_SESSION['playerID'];

// Fetch player details for the logged-in player
$stmt = $pdo->prepare("SELECT * FROM players WHERE playerID = :playerID");
$stmt->execute(['playerID' => $playerID]);
$player = $stmt->fetch();

if (!$player) {
    // Handle case where player data is not found
    echo "Player data not found.";
    exit;
}

function getCareerHighlights($pdo, $playerID) {
    // SQL query to fetch the latest 3 records per category
    $stmt = $pdo->prepare("
        SELECT category, description, year 
        FROM (
            SELECT category, description, year, 
                   ROW_NUMBER() OVER (PARTITION BY category ORDER BY year DESC) AS row_num
            FROM career_highlights 
            WHERE playerID = :playerID
        ) AS ranked
        WHERE row_num <= 3
    ");
    
    $stmt->execute(['playerID' => $playerID]);
    $highlights = $stmt->fetchAll();

    // Group highlights by category
    $groupedHighlights = [
        'Award' => [],
        'Team Achievement' => [],
        'Team History' => []
    ];

    foreach ($highlights as $highlight) {
        $groupedHighlights[$highlight['category']][] = $highlight;
    }

    return $groupedHighlights;
}
function getPlayerAverages($pdo, $playerID) {
    // SQL query to calculate averages for Points (PPG), Rebounds (RBG), and Assists (APG)
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(AVG(points), 0) AS PPG, 
            COALESCE(AVG(rebounds), 0) AS RBG, 
            COALESCE(AVG(assists), 0) AS APG
        FROM game_stats
        WHERE playerID = :playerID
    ");
    
    $stmt->execute(['playerID' => $playerID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Calculate age from birthdate
$birthdate = new DateTime($player['birthdate']);
$now = new DateTime();
$age = $now->diff($birthdate)->y;

// Fetch active players excluding the logged-in player
$stmt = $pdo->prepare("SELECT playerID, firstName, lastName, jerseyNumber, position 
                        FROM players WHERE status = 'Active' AND playerID != :playerID AND teamID = 1");
$stmt->execute(['playerID' => $playerID]);
$otherPlayers = $stmt->fetchAll();

$careerHighlights = getCareerHighlights($pdo, $playerID);
$playerAverages = getPlayerAverages($pdo, $playerID);
$ppg = number_format($playerAverages['PPG'], 1);
$rbg = number_format($playerAverages['RBG'], 1);
$apg = number_format($playerAverages['APG'], 1);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="player-profile">
        <!-- Navigation Bar -->
    <div class="navbar">
      <div class="logo-container">
        <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
      </div>
      <div class="nav-links">
        <ul>
          <li><a href="#" class="active">PLAYER PROFILE</a></li>
          <li><a href="/gameplan/Schedule_Player/PlayerSM.php">SCHEDULE</a></li>
          <li><a href="/gameplan/PGM_Player/PGM.php">PROGRESS & MILESTONE</a></li>
          <li><a href="/gameplan/Login.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
      </div>
    </div>   

        <!-- Player Profile Section -->
        <section class="profile-section">
            <div class="profile-header">
                <img src="Player.png" alt="Player Silhouette" class="player-image">
                <div class="player-info">
                    <h4>NU BULLDOGS</h4>
                    <h2><?php echo $player['firstName'] . ' ' . $player['lastName']; ?></h2>
                    <p># <?php echo $player['jerseyNumber']; ?></p>
                    <p><?php echo $player['position']; ?></p>
                </div>
                <img src="Bulldog.png" alt="Team Logo" class="team-logo">
            </div>
        
            <!-- Player Stats Section -->
            <div class="player-details">
                <div class="stats">
                    <div class="stat">PPG <span><?php echo $ppg; ?></span></div>
                    <div class="stat">RBG <span><?php echo $rbg; ?></span></div>
                    <div class="stat">APG <span><?php echo $apg; ?></span></div>
                </div>
        
                <div class="additional-info">
                    <div class="info-group">Height: <span><?php echo $player['height']; ?></span></div>
                    <div class="info-group">Weight: <span><?php echo $player['weight']; ?></span></div>
                    <div class="info-group">Age: <span><?php echo $age; ?> years</span></div>
                    <div class="info-group">Birthdate: <span><?php echo date("F j, Y", strtotime($player['birthdate'])); ?></span></div>
                </div>
            </div>
        </section>

      <!-- Career Highlights Section -->
        <section class="career-highlights">
            <h3>Career Highlights</h3>
            <div class="highlights-content">
                
                <!-- Awards and Honors -->
                <?php if (!empty($careerHighlights['Award'])): ?>
                <div class="highlight-group">
                    <h4>Awards and Honors</h4>
                    <ul>
                        <?php foreach ($careerHighlights['Award'] as $award): ?>
                            <li><?php echo htmlspecialchars($award['description']); ?><?php echo $award['year'] ? " - " . htmlspecialchars($award['year']) : ""; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Team Achievements -->
                <?php if (!empty($careerHighlights['Team Achievement'])): ?>
                <div class="highlight-group">
                    <h4>Team Achievements</h4>
                    <ul>
                        <?php foreach ($careerHighlights['Team Achievement'] as $achievement): ?>
                            <li><?php echo htmlspecialchars($achievement['description']); ?><?php echo $achievement['year'] ? " - " . htmlspecialchars($achievement['year']) : ""; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Team History -->
                <?php if (!empty($careerHighlights['Team History'])): ?>
                <div class="highlight-group">
                    <h4>Team History</h4>
                    <ul>
                        <?php foreach ($careerHighlights['Team History'] as $history): ?>
                            <li><?php echo htmlspecialchars($history['description']); ?><?php echo $history['year'] ? ": " . htmlspecialchars($history['year']) : ""; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </section>

        <!-- More From the Roster Section -->
        <section class="roster">
                <h3>More From the Roster</h3>
                <div class="roster-list">
                    <?php foreach ($otherPlayers as $otherPlayer): ?>
                        <div class="roster-item">
                            <img src="Vacant Player.png" alt="Player">
                            <span>#<?php echo $otherPlayer['jerseyNumber']; ?> <?php echo $otherPlayer['position']; ?></span>
                            <span><?php echo $otherPlayer['firstName'] . ' ' . $otherPlayer['lastName']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
    </div>
</body>
</html>