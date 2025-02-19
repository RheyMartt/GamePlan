<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath

if (!isset($pdo)) {
    die("Database connection not established.");
}

$players = [];
$games = [];

// Fetch only games that have recorded stats
$query = "SELECT 
            g.gameID, 
            ht.teamName AS homeTeam, 
            at.teamName AS awayTeam, 
            g.gameDate 
          FROM games g
          JOIN teams ht ON g.homeTeamID = ht.teamID
          JOIN teams at ON g.awayTeamID = at.teamID
          WHERE EXISTS (
              SELECT 1 FROM game_stats gs WHERE gs.gameID = g.gameID
          )
          ORDER BY g.gameDate DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);


try {
    // Fetch player stats for NU team with updated column names
    $sql = "SELECT 
            p.firstName, p.lastName, p.position, 
            s.points, s.assists, s.rebounds, s.steals, s.blocks, s.turnovers, 
            s.minutesPlayed, s.fieldGoalsMade, s.fieldGoalsAttempted, 
            (s.fieldGoalsMade / NULLIF(s.fieldGoalsAttempted, 0)) * 100 AS fieldGoalsPercentage,
            s.threePointersMade, s.threePointersAttempted, 
            (s.threePointersMade / NULLIF(s.threePointersAttempted, 0)) * 100 AS threePointsPercentage,
            s.freeThrowsMade, s.freeThrowsAttempted, 
            (s.freeThrowsMade / NULLIF(s.freeThrowsAttempted, 0)) * 100 AS freeThrowPercentage,
            s.plusMinus,
            t.teamName  -- Fetch team name from teams table
        FROM game_stats s
        JOIN players p ON s.playerID = p.playerID
        JOIN teams t ON p.teamID = t.teamID  -- Join teams table to get team name
        WHERE p.teamID = (SELECT teamID FROM teams WHERE teamName = 'NU')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="fnastyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo-container">
            <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
        </div>
        <div class="nav-links">
            <ul>
                <li><a href="/gameplan/Dashboard_AnR/GD.php">GAME DASHBOARD</a></li>
                <li><a href="#">TEAM COMMUNICATION</a></li>
                <li><a href="/gameplan/PM_AnR/PM.html">PLAYER MANAGEMENT</a></li>
                <li><a href="#" class="active">FEEDBACK & ANALYZATION</a></li>
                <li><a href="/gameplan/PGM_AnR/PGM.html">PROGRESS & MILESTONE</a></li>
                <li><a href="/gameplan/Resource_Management_AnR/RM.html">RESOURCES</a></li>
                <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <div class="left-panel">
        <div class="dropdown-wrapper">
            <select id="gameDropdown" class="first-dropdown">
                <option value="">Select a Game</option>
                <?php foreach ($games as $game): ?>
                    <option value="<?= $game['gameID'] ?>">
                    <?= "Game: " . htmlspecialchars($game['homeTeam']) . " vs " . htmlspecialchars($game['awayTeam']) . " (" . $game['gameDate'] . ")" ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>




            <!-- NU Players Table -->
            <div class="player-stats-container">
                <div class="player-stats">
                    <h2>NU Player Stats</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Player Name</th>
                                <th>Pos</th>
                                <th>Pts</th>
                                <th>Asts</th>
                                <th>Rbs</th>
                                <th>Stls</th>
                                <th>Blks</th>
                                <th>TO</th>
                                <th>MP</th>
                                <th>FGM</th>
                                <th>FGA</th>
                                <th>FG%</th>
                                <th>TPM</th>
                                <th>TPA</th>
                                <th>TP%</th>
                                <th>FTM</th>
                                <th>FTA</th>
                                <th>FT%</th>
                                <th>+/-</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player): ?>
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
        </div>
        
        <div class="right-panel">
            <h2>NARRATIVE REPORT</h2>
            <div id="gameInfo">GAME: - &nbsp;&nbsp;&nbsp; DATE: -</div>
            <h3>FINDINGS & GAME ANALYSIS</h3>
            <textarea class="fixed-textbox" placeholder="Game analysis content will appear here."></textarea>
            <h3>CONCLUSION</h3>
            <textarea class="fixed-textbox" placeholder="Conclusion content will appear here."></textarea>
            <h3>KEY FINDINGS</h3>
            <textarea class="fixed-textbox" placeholder="Key findings content will appear here."></textarea>
            <button class="submit">SUBMIT</button>
        </div>
    </div>

    <script src="fnascript.js"></script>

</body>
</html>
