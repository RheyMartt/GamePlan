<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Ensure the path is correct

if (!isset($_GET['gameID'])) {
    echo json_encode(["error" => "Game ID is required."]);
    exit;
}

$gameID = $_GET['gameID'];

try {
    $sql = "
        SELECT 
            p.firstName,
            p.lastName,
            p.position,
            gs.points,
            gs.assists,
            gs.rebounds,
            gs.steals,
            gs.blocks,
            gs.turnovers,
            gs.minutesPlayed,
            gs.fieldGoalsMade,
            gs.fieldGoalsAttempted,
            gs.fieldGoalsPercentage,
            gs.threePointersMade,
            gs.threePointersAttempted,
            gs.threePointsPercentage,
            gs.freeThrowsMade,
            gs.freeThrowsAttempted,
            gs.freeThrowPercentage,
            gs.plusMinus
        FROM game_stats gs
        INNER JOIN players p ON gs.playerID = p.playerID
        WHERE gs.gameID = :gameID
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($stats);
} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>

