<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath

header('Content-Type: application/json');

// Check if playerID is provided
if (isset($_POST['playerID'])) {
    $playerID = $_POST['playerID'];

    // Log the playerID to debug
    error_log("PlayerID received: " . $playerID);

    if ($pdo instanceof PDO) {
        // Fetch player data (bio information)
        $query = "SELECT firstName, lastName, position, status, height, weight FROM players WHERE playerID = :playerID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $player = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($player) {
                // Return player bio data as JSON
                echo json_encode([
                    'firstName' => $player['firstName'],
                    'lastName' => $player['lastName'],
                    'position' => $player['position'],
                    'status' => $player['status'],
                    'height' => $player['height'],
                    'weight' => $player['weight']
                ]);
            } else {
                // Return error message if player is not found
                echo json_encode(['error' => 'Player not found']);
            }
        } else {
            echo json_encode(['error' => 'Database query failed']);
        }
    } else {
        echo json_encode(['error' => 'Database connection failed']);
    }
} else {
    echo json_encode(['error' => 'Player ID not provided']);
}
?>
