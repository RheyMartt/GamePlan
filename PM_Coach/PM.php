<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GamePlan/connection.php';

// Fetch player data
$playerID = isset($_GET['playerID']) ? $_GET['playerID'] : 1; // Default player ID

try {
    $stmt = $pdo->prepare("SELECT firstName, lastName, position, status, height, weight FROM players WHERE playerID = :playerID");
    $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
    $stmt->execute();
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
            <button class="add-player-btn">Add Player</button>
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
                    echo '<img src="LBJ.png" alt="Player" class="player-btn" data-playerid="' . $player['playerID'] . '">';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

         <!-- New Container with 3 Sections, stacked vertically -->
         <div class="container">
            <!-- Section 1: Bio/History/Status -->
            <div class="section">
                <h3>Bio</h3>
                <p>Name: <?php echo htmlspecialchars($player['firstName'] . ' ' . $player['lastName']) ?? 'N/A'; ?></p>
                <p>Position: <?php echo htmlspecialchars($player['position'] ?? 'N/A'); ?></p>
                <p>Status: <?php echo htmlspecialchars($player['status'] ?? 'N/A'); ?></p>
                <p>Height: <?php echo htmlspecialchars($player['height'] ?? 'N/A'); ?> | Weight: <?php echo htmlspecialchars($player['weight'] ?? 'N/A'); ?></p>
            </div>

           <!-- Section 2: Stats (Based on Season) -->
           <div class="section">
            <div class="dropdown">
                <button class="dropbtn">Choose Season</button>
                <div class="dropdown-content">
                    <ul>
                        <li><a href="#">S84</a></li>
                        <li><a href="#">S85</a></li>
                        <li><a href="#">S86</a></li>
                        <li><a href="#">S87</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Stats</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Points Per Game</td>
                            <td>20.3</td>
                        </tr>
                        <tr>
                            <td>Assists Per Game</td>
                            <td>6.5</td>
                        </tr>
                        <tr>
                            <td>Rebounds Per Game</td>
                            <td>5.1</td>
                        </tr>
                        <tr>
                            <td>Blocks Per Game</td>
                            <td>1.0</td>
                        </tr>
                        <tr>
                            <td>Steals Per Game</td>
                            <td>2.3</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <!-- Attendance Section -->
            <div class="section" alt="Section 3">
                <h3>Attendance</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Practices</td>
                                <td>40</td>
                            </tr>
                            <tr>
                                <td>Attended</td>
                                <td>38</td>
                            </tr>
                            <tr>
                                <td>Missed</td>
                                <td>2</td>
                            </tr>
                            <tr>
                                <td>Last Attendance</td>
                                <td>January 20, 2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Buttons Below Section 3 -->
            <div class="buttons-container">
                <button class="classify-injured-btn">CLASSIFY AS INJURED</button>
                <button class="remove-player-btn">REMOVE</button>
                <button class="edit-player-btn">EDIT PLAYER</button>
            </div>
        </div>
    </div>

    <!-- The Modal -->
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
</body>
<script src="pmcscript.js"></script>
</html>