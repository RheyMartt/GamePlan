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

// Calculate age from birthdate
$birthdate = new DateTime($player['birthdate']);
$now = new DateTime();
$age = $now->diff($birthdate)->y;

// Fetch active players excluding the logged-in player
$stmt = $pdo->prepare("SELECT playerID, firstName, lastName, jerseyNumber, position 
                        FROM players WHERE status = 'Active' AND playerID != :playerID AND teamID = 1");
$stmt->execute(['playerID' => $playerID]);
$otherPlayers = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
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
          <li><a href="/gameplan/Com/CommHub.html">TEAM COMMUNICATION</a></li>
          <li><a href="/gameplan/Schedule_Player/PlayerSM.php">SCHEDULE</a></li>
          <li><a href="/gameplan/PGM_Player/PGM.php">PROGRESS & MILESTONE</a></li>
          <li><a href="/gameplan/Resource_Management_Player/RM.php">RESOURCES</a></li>
          <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
      </div>
    </div>   

        <!-- Player Profile Section -->
        <section class="profile-section">
            <div class="profile-header">
                <img src="LBJ.png" alt="Player Silhouette" class="player-image">
                <div class="player-info">
                    <h4>NU BULLDOGS</h4>
                    <h2><?php echo $player['firstName'] . ' ' . $player['lastName']; ?></h2>
                    <p># <?php echo $player['jerseyNumber']; ?></p>
                    <p><?php echo $player['position']; ?></p>
                </div>
                <img src="Bulldog.png" alt="Team Logo" class="team-logo">
            </div>
        
            <div class="player-details">
                <div class="stats">
                    <div class="stat">PPG <span>23.8</span></div>
                    <div class="stat">RBG <span>7.7</span></div>
                    <div class="stat">APG <span>8.8</span></div>
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
              <div class="highlight-group">
                  <h4>Awards and Honors</h4>
                  <ul>
                      <li>Most Valuable Player (MVP) - 2022</li>
                      <li>All-Star Selection - 3 Times</li>
                      <li>Defensive Player of the Year - 2021</li>
                  </ul>
              </div>

              <!-- Team Achievements -->
              <div class="highlight-group">
                  <h4>Team Achievements</h4>
                  <ul>
                      <li>National Champions - 2022</li>
                      <li>Regional Champions - 2021, 2023</li>
                      <li>Best Offensive Team - 2022</li>
                  </ul>
              </div>

              <!-- Personal Records -->
              <div class="highlight-group">
                  <h4>Personal Records</h4>
                  <ul>
                      <li>Career-High Points in a Game: 55</li>
                      <li>Triple-Doubles: 25</li>
                      <li>Consecutive Games Scoring 30+: 10</li>
                  </ul>
              </div>
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