<?php
// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/GamePlan/connection.php';

if (isset($_POST['playerID'])) {
    $playerID = $_POST['playerID'];

    try {
        // Check if the player exists in the players table
        $stmt = $pdo->prepare("SELECT * FROM players WHERE playerID = :playerID");
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->execute();
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$player) {
            // Return error if player does not exist
            echo json_encode(['error' => 'Player not found']);
            exit;
        }

        // Query to get player stats from the game_stats table
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT gameID) AS games_played, 
                COALESCE(SUM(points), 0) AS total_points, 
                COALESCE(SUM(assists), 0) AS total_assists, 
                COALESCE(SUM(rebounds), 0) AS total_rebounds, 
                COALESCE(SUM(blocks), 0) AS total_blocks, 
                COALESCE(SUM(steals), 0) AS total_steals 
            FROM game_stats
            WHERE playerID = :playerID
        ");
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stats) {
            // Calculate per game stats
            $gamesPlayed = (int) $stats['games_played'];
            $totalPoints = (int) $stats['total_points'];
            $totalAssists = (int) $stats['total_assists'];
            $totalRebounds = (int) $stats['total_rebounds'];
            $totalBlocks = (int) $stats['total_blocks'];
            $totalSteals = (int) $stats['total_steals'];

            $ppg = $gamesPlayed > 0 ? number_format($totalPoints / $gamesPlayed, 1) : 0;
            $apg = $gamesPlayed > 0 ? number_format($totalAssists / $gamesPlayed, 1) : 0;
            $rpg = $gamesPlayed > 0 ? number_format($totalRebounds / $gamesPlayed, 1) : 0;
            $bpg = $gamesPlayed > 0 ? number_format($totalBlocks / $gamesPlayed, 1) : 0;
            $spg = $gamesPlayed > 0 ? number_format($totalSteals / $gamesPlayed, 1) : 0;

            // Return stats as JSON
            echo json_encode([
                'games_played' => $gamesPlayed,
                'ppg' => $ppg,
                'apg' => $apg,
                'rpg' => $rpg,
                'bpg' => $bpg,
                'spg' => $spg
            ]);
        } else {
            // If no stats found for the player
            echo json_encode(['error' => 'No stats found for this player']);
        }

    } catch (PDOException $e) {
        // Handle database connection error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // If player ID is not provided in the request
    echo json_encode(['error' => 'Player ID not provided']);
}
?>
