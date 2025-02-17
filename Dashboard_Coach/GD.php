<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GamePlan/connection.php';

// Fetch all games for dropdown
try {
    $gameQuery = $pdo->query("SELECT g.gameID, h.teamName AS homeTeam, a.teamName AS awayTeam 
                              FROM games g
                              JOIN teams h ON g.homeTeamID = h.teamID
                              JOIN teams a ON g.awayTeamID = a.teamID
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
        <li><a href="/gameplan/Com/CommHub.html">TEAM COMMUNICATION</a></li>
        <li><a href="/gameplan/PM_Coach/PM.html">PLAYER MANAGEMENT</a></li>
        <li><a href="/gameplan/Schedule_Coach/SM.html">SCHEDULE</a></li>
        <li><a href="/gameplan/PGM_coach/PGM.php">PROGRESS & MILESTONE</a></li>
        <li><a href="/gameplan/Resource_Management_Coach/RM.html">RESOURCES</a></li>
        <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
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

    <?php if ($gameDetails): ?>
      <div class="game-details">
        <h1>
          <span><?php echo $gameDetails['homeTeam']; ?></span>
          <span>-- vs --</span>
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
  </main>
</body>
</html>
