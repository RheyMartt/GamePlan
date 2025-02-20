<?php
header('Content-Type: application/json');

include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Adjust this to your database connection file

if (!isset($_GET['gameID']) || empty($_GET['gameID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Game ID is required.']);
    exit;
}

$gameID = (int)$_GET['gameID'];

try {
    // Fetch the opponent ID and home/away information for the game
    $stmtGame = $pdo->prepare("SELECT homeTeamID, awayTeamID FROM games WHERE gameID = ?");
    $stmtGame->execute([$gameID]);
    $game = $stmtGame->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid game ID.']);
        exit;
    }

    $homeTeamID = $game['homeTeamID'];
    $awayTeamID = $game['awayTeamID'];

    // Fetch players for the home team (NU Bulldogs)
    $stmtHomePlayers = $pdo->prepare("SELECT playerID, firstName, lastName FROM players WHERE teamID = ?");
    $stmtHomePlayers->execute([$homeTeamID]);
    $homePlayers = $stmtHomePlayers->fetchAll(PDO::FETCH_ASSOC);

    // Fetch players for the away team (opponent)
    $stmtAwayPlayers = $pdo->prepare("SELECT playerID, firstName, lastName FROM players WHERE teamID = ?");
    $stmtAwayPlayers->execute([$awayTeamID]);
    $awayPlayers = $stmtAwayPlayers->fetchAll(PDO::FETCH_ASSOC);

    // Return both home and away players
    echo json_encode([
        'status' => 'success',
        'homePlayers' => $homePlayers,
        'awayPlayers' => $awayPlayers,
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
