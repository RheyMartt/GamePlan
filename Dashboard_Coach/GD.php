<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath

// Fetch all games for dropdown
try {
  $gameQuery = $pdo->query("SELECT DISTINCT g.gameID, h.teamName AS homeTeam, a.teamName AS awayTeam 
                            FROM games g
                            JOIN teams h ON g.homeTeamID = h.teamID
                            JOIN teams a ON g.awayTeamID = a.teamID
                            JOIN game_stats gs ON g.gameID = gs.gameID
                            ORDER BY g.gameDate DESC");
    $games = $gameQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Get selected gameID (default to the latest game)
$gameID = isset($_GET['gameID']) ? $_GET['gameID'] : ($games[0]['gameID'] ?? null);

// Fetch game details
$gameDetails = null;
if ($gameID) {
    try {
        $stmt = $pdo->prepare("SELECT g.*, 
                                      h.teamName AS homeTeam, 
                                      a.teamName AS awayTeam
                               FROM games g
                               JOIN teams h ON g.homeTeamID = h.teamID
                               JOIN teams a ON g.awayTeamID = a.teamID
                               WHERE g.gameID = :gameID");
        $stmt->execute(['gameID' => $gameID]);
        $gameDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

$nuStats = [];
$opponentStats = [];

if ($gameID && $gameDetails) {
    $opponentTeamID = ($gameDetails['homeTeamID'] == 1) ? $gameDetails['awayTeamID'] : $gameDetails['homeTeamID'];

    try {
        // NU Bulldogs Stats (Always teamID = 1)
        $nuStmt = $pdo->prepare("SELECT p.firstName, p.lastName, p.position, p.jerseyNumber, 
                                         gs.*
                                  FROM game_stats gs
                                  JOIN players p ON gs.playerID = p.playerID
                                  WHERE gs.gameID = :gameID AND p.teamID = 1");
        $nuStmt->execute(['gameID' => $gameID]);
        $nuStats = $nuStmt->fetchAll(PDO::FETCH_ASSOC);

        // Opponent Stats (Dynamic teamID)
        $oppStmt = $pdo->prepare("SELECT p.firstName, p.lastName, p.position, p.jerseyNumber, 
                                          gs.*
                                   FROM game_stats gs
                                   JOIN players p ON gs.playerID = p.playerID
                                   WHERE gs.gameID = :gameID AND p.teamID = :opponentTeamID");
        $oppStmt->execute(['gameID' => $gameID, 'opponentTeamID' => $opponentTeamID]);
        $opponentStats = $oppStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

$topPerformers = [];
if ($gameID) {
    try {
        $topStmt = $pdo->prepare("SELECT p.firstName, p.lastName, gs.points, gs.rebounds, gs.assists, gs.steals
                                  FROM game_stats gs
                                  JOIN players p ON gs.playerID = p.playerID
                                  WHERE gs.gameID = :gameID AND p.teamID = 1
                                  ORDER BY gs.points DESC
                                  LIMIT 5");
        $topStmt->execute(['gameID' => $gameID]);
        $topPerformers = $topStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NU GAMEPLAN</title>
  <link rel="stylesheet" href="gdCstyles.css">    
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <style>
    .active {
        font-weight: bold;
    }
  </style>

  <!-- Navigation Bar -->
  <div class="navbar">
    <div class="logo-container">
      <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
    </div>
    <div class="nav-links">
      <ul>
        <li><a href="#" class="active">GAME DASHBOARD</a></li>
        <li><a href="/gameplan/PM_Coach/PM.php">PLAYER MANAGEMENT</a></li>
        <li><a href="/gameplan/Schedule_Coach/SM.php">SCHEDULE</a></li>
        <li><a href="/gameplan/PGM_coach/PGM.php">PROGRESS & MILESTONE</a></li>
        <li><a href="/gamplan/Login.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
      </ul>
    </div>
  </div>

  <main class="main-content">
    <!-- Dropdown menu for game selection -->
    <div class="dropdown" id="gameDropdown">
      <button id="dropdownButton"><?php echo $gameDetails ? "{$gameDetails['homeTeam']} vs. {$gameDetails['awayTeam']}" : "Select a game"; ?></button>
      <div class="dropdown-content">
        <?php foreach ($games as $game): ?>
          <a href="?gameID=<?php echo $game['gameID']; ?>">
            <?php echo "{$game['homeTeam']} vs. {$game['awayTeam']}"; ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <button onclick="openModal('addGameModal')">Add New Game</button>

    <!-- Add Game Modal -->
    <div id="addGameModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal('addGameModal')">&times;</span>
        <h2>Add New Game</h2>
        <form method="POST" action="add_game.php">
        <label for="opponent">Opponent Team:</label>
        <select id="opponent" name="opponentID" required>
            <option value="">Select Opponent</option>
            <?php
            $opponents = $pdo->query("SELECT * FROM teams WHERE teamID != 1")->fetchAll();
            foreach ($opponents as $team) {
                echo "<option value='{$team['teamID']}'>{$team['teamName']}</option>"; // No <br> or <b>
            }
            ?>
        </select>

        <label for="gameDate">Game Date:</label>
        <input type="date" name="gameDate" required>

        <label for="gameTime">Game Time:</label>
        <input type="time" name="gameTime" required>

        <label for="gameLocation">Location:</label>
        <input type="text" name="gameLocation" required>

        <label for="gameType">Game Type:</label>
        <select name="gameType">
            <option value="Official">Official</option>
            <option value="Exhibition">Exhibition</option>
            <option value="Practice">Practice</option>
        </select>

        <h3>Quarter Scores</h3>
        <label>Home Q1:</label> <input type="number" name="homeQuarterOne" min="0">
        <label>Home Q2:</label> <input type="number" name="homeQuarterTwo" min="0">
        <label>Home Q3:</label> <input type="number" name="homeQuarterThree" min="0">
        <label>Home Q4:</label> <input type="number" name="homeQuarterFour" min="0">
        <label>Home Final Score:</label> <input type="number" name="homeFinalScore" min="0">

        <label>Away Q1:</label> <input type="number" name="awayQuarterOne" min="0">
        <label>Away Q2:</label> <input type="number" name="awayQuarterTwo" min="0">
        <label>Away Q3:</label> <input type="number" name="awayQuarterThree" min="0">
        <label>Away Q4:</label> <input type="number" name="awayQuarterFour" min="0">
        <label>Away Final Score:</label> <input type="number" name="awayFinalScore" min="0">

          <button type="submit" name="addGame" value="1">Save Game</button>
          <button type="button" onclick="openModal('addStatsModal')">Add Player Stats</button>
          </form>
      </div>
    </div>

    <!-- Add Player Stats Modal -->
    <div id="addStatsModal" class="modal">
      <div class="modal-content">
      <span class="close" onclick="closeModal('addStatsModal')">&times;</span>
      <h2>Add Player Stats</h2>
        <form method="POST" action="add_stats.php">
          <label>Select Player:</label>
          <select name="playerID" required>
              <option value="">Select Player</option>
              <?php
              try {
                  // Ensure $opponentID is set and valid
                  $opponentID = isset($_POST['opponentID']) ? (int)$_POST['opponentID'] : 0;
                  $yourTeamID = 1; // Change this if needed

                  // Use prepared statement to prevent SQL injection
                  $stmt = $pdo->prepare("SELECT playerID, firstName, lastName FROM players WHERE teamID = ? OR teamID = ?");
                  $stmt->execute([$yourTeamID, $opponentID]);
                  $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($players as $player) {
                      echo "<option value='{$player['playerID']}'>{$player['firstName']} {$player['lastName']}</option>";
                  }
              } catch (PDOException $e) {
                  die("Database error: " . $e->getMessage());
              }
              ?>
          </select>


          <label>Points:</label> <input type="number" name="points" required>
          <label>Assists:</label> <input type="number" name="assists" required>
          <label>Rebounds:</label> <input type="number" name="rebounds" required>
          <label>Steals:</label> <input type="number" name="steals" required>
          <label>Blocks:</label> <input type="number" name="blocks" required>
          <label>Turnovers:</label> <input type="number" name="turnovers" required>
          <label>Minutes Played:</label> <input type="number" name="minutesPlayed" required>

          <button type="submit" name="addStats">Save Stats</button>
        </form>
      </div>
    </div>

    

    <!-- Game Details -->
    <?php if ($gameDetails): ?>
      <div class="game-details">
        <h1>
          <span><?php echo $gameDetails['homeTeam']; ?></span>
          <span> vs </span>
          <span><?php echo $gameDetails['awayTeam']; ?></span>
        </h1>
        <p><?php echo $gameDetails['gameDate']; ?> | <?php echo $gameDetails['gameLocation']; ?></p>
        <p>Game Type: <?php echo $gameDetails['gameType']; ?></p>

        <!-- Quarter Scores -->
        <div class="score-breakdown">
          <table>
            <thead>
              <tr>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Final</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $gameDetails['homeQuarterOne']; ?></td>
                <td><?php echo $gameDetails['homeQuarterTwo']; ?></td>
                <td><?php echo $gameDetails['homeQuarterThree']; ?></td>
                <td><?php echo $gameDetails['homeQuarterFour']; ?></td>
                <td><?php echo $gameDetails['homeFinalScore']; ?></td>
              </tr>
              <tr>
                <td><?php echo $gameDetails['awayQuarterOne']; ?></td>
                <td><?php echo $gameDetails['awayQuarterTwo']; ?></td>
                <td><?php echo $gameDetails['awayQuarterThree']; ?></td>
                <td><?php echo $gameDetails['awayQuarterFour']; ?></td>
                <td><?php echo $gameDetails['awayFinalScore']; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Top Scorers Section -->
    <div class="topperformers-container">
        <h2>Top Performers</h2>
        <div class="topperformersList">
        <ul>
          <?php foreach ($topPerformers as $performer): ?>
            <li>
              <strong><?php echo "{$performer['firstName']} {$performer['lastName']}"; ?></strong><br>
              Pts: <?php echo $performer['points']; ?> |
              Rbs: <?php echo $performer['rebounds']; ?> |
              Stls: <?php echo $performer['steals']; ?> |
              Asts: <?php echo $performer['assists']; ?>
            </li>
          <?php endforeach; ?>
        </ul>
        </div>
      </div>

    <!-- NU Bulldogs Player Statistics -->
    <div class="player-stats-container">
        <h2>NU Bulldogs Player Statistics</h2>
        <div class="player-stats">
        <table>
            <thead>
                <tr>
                    <th>Player Name</th>
                    <th>Position</th>
                    <th>Pts</th>
                    <th>Asts</th>
                    <th>Rbs</th>
                    <th>Stls</th>
                    <th>Blks</th>
                    <th>TOs</th>
                    <th>Minutes</th>
                    <th>FGM</th>
                    <th>FGA</th>
                    <th>FG%</th>
                    <th>3PM</th>
                    <th>3PA</th>
                    <th>3P%</th>
                    <th>FTM</th>
                    <th>FTA</th>
                    <th>FT%</th>
                    <th>+/-</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nuStats as $stat): ?>
                <tr>
                    <td><?php echo "{$stat['firstName']} {$stat['lastName']}"; ?></td>
                    <td><?php echo $stat['position']; ?></td>
                    <td><?php echo $stat['points']; ?></td>
                    <td><?php echo $stat['assists']; ?></td>
                    <td><?php echo $stat['rebounds']; ?></td>
                    <td><?php echo $stat['steals']; ?></td>
                    <td><?php echo $stat['blocks']; ?></td>
                    <td><?php echo $stat['turnovers']; ?></td>
                    <td><?php echo $stat['minutesPlayed']; ?></td>
                    <td><?php echo $stat['fieldGoalsMade']; ?></td>
                    <td><?php echo $stat['fieldGoalsAttempted']; ?></td>
                    <td><?php echo $stat['fieldGoalsPercentage']; ?>%</td>
                    <td><?php echo $stat['threePointersMade']; ?></td>
                    <td><?php echo $stat['threePointersAttempted']; ?></td>
                    <td><?php echo $stat['threePointsPercentage']; ?>%</td>
                    <td><?php echo $stat['freeThrowsMade']; ?></td>
                    <td><?php echo $stat['freeThrowsAttempted']; ?></td>
                    <td><?php echo $stat['freeThrowPercentage']; ?>%</td>
                    <td><?php echo $stat['plusMinus']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
    </div>

    <!-- Opponent Player Statistics -->
    <div class="player-stats-container">
        <h2><?php echo $gameDetails['awayTeam']; ?> Player Statistics</h2>
        <div class="player-stats">
        <table>
            <thead>
                <tr>
                    <th>Player Name</th>
                    <th>Team</th>
                    <th>Position</th>
                    <th>Pts</th>
                    <th>Asts</th>
                    <th>Rbs</th>
                    <th>Stls</th>
                    <th>Blks</th>
                    <th>TOs</th>
                    <th>Minutes</th>
                    <th>FGM</th>
                    <th>FGA</th>
                    <th>FG%</th>
                    <th>3PM</th>
                    <th>3PA</th>
                    <th>3P%</th>
                    <th>FTM</th>
                    <th>FTA</th>
                    <th>FT%</th>
                    <th>+/-</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($opponentStats as $stat): ?>
                <tr>
                    <td><?php echo "{$stat['firstName']} {$stat['lastName']}"; ?></td>
                    <td><?php echo $stat['position']; ?></td>
                    <td><?php echo $stat['points']; ?></td>
                    <td><?php echo $stat['assists']; ?></td>
                    <td><?php echo $stat['rebounds']; ?></td>
                    <td><?php echo $stat['steals']; ?></td>
                    <td><?php echo $stat['blocks']; ?></td>
                    <td><?php echo $stat['turnovers']; ?></td>
                    <td><?php echo $stat['minutesPlayed']; ?></td>
                    <td><?php echo $stat['fieldGoalsMade']; ?></td>
                    <td><?php echo $stat['fieldGoalsAttempted']; ?></td>
                    <td><?php echo $stat['fieldGoalsPercentage']; ?>%</td>
                    <td><?php echo $stat['threePointersMade']; ?></td>
                    <td><?php echo $stat['threePointersAttempted']; ?></td>
                    <td><?php echo $stat['threePointsPercentage']; ?>%</td>
                    <td><?php echo $stat['freeThrowsMade']; ?></td>
                    <td><?php echo $stat['freeThrowsAttempted']; ?></td>
                    <td><?php echo $stat['freeThrowPercentage']; ?>%</td>
                    <td><?php echo $stat['plusMinus']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <?php else: ?>
      <p>No game data available.</p>
    <?php endif; ?>

    <script src="gdcscript.js"></script>

  </main>
</body>
</html>
