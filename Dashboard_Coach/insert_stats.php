<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the response content type to JSON
header('Content-Type: application/json');

try {
    // Include your database connection
    include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';// Replace with your actual DB connection script

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method. Use POST.");
    }

    // Retrieve POST data
    $gameID = $_POST['gameID'] ?? null;
    $homePlayerID = $_POST['homePlayerID'] ?? null;
    $awayPlayerID = $_POST['awayPlayerID'] ?? null;

    // Home player stats
    $homePoints = $_POST['homePoints'] ?? null;
    $homeAssists = $_POST['homeAssists'] ?? null;
    $homeRebounds = $_POST['homeRebounds'] ?? null;
    $homeSteals = $_POST['homeSteals'] ?? null;
    $homeBlocks = $_POST['homeBlocks'] ?? null;
    $homeTurnovers = $_POST['homeTurnovers'] ?? null;
    $homeMinutesPlayed = $_POST['homeMinutesPlayed'] ?? null;

    // Away player stats
    $awayPoints = $_POST['awayPoints'] ?? null;
    $awayAssists = $_POST['awayAssists'] ?? null;
    $awayRebounds = $_POST['awayRebounds'] ?? null;
    $awaySteals = $_POST['awaySteals'] ?? null;
    $awayBlocks = $_POST['awayBlocks'] ?? null;
    $awayTurnovers = $_POST['awayTurnovers'] ?? null;
    $awayMinutesPlayed = $_POST['awayMinutesPlayed'] ?? null;

    // Validate required inputs
    if (!$gameID || !$homePlayerID || !$awayPlayerID) {
        throw new Exception("Missing required fields: gameID, homePlayerID, or awayPlayerID.");
    }

    // Validate numeric inputs (basic validation)
    $numericFields = [
        $homePoints, $homeAssists, $homeRebounds, $homeSteals, $homeBlocks, $homeTurnovers, $homeMinutesPlayed,
        $awayPoints, $awayAssists, $awayRebounds, $awaySteals, $awayBlocks, $awayTurnovers, $awayMinutesPlayed
    ];

    foreach ($numericFields as $field) {
        if (!is_numeric($field)) {
            throw new Exception("All stats fields must be numeric.");
        }
    }

    // Insert home player stats
    $stmt = $pdo->prepare("
        INSERT INTO game_stats (gameID, playerID, points, assists, rebounds, steals, blocks, turnovers, minutesPlayed)
        VALUES (:gameID, :playerID, :points, :assists, :rebounds, :steals, :blocks, :turnovers, :minutesPlayed)
    ");
    $stmt->execute([
        ':gameID' => $gameID,
        ':playerID' => $homePlayerID,
        ':points' => $homePoints,
        ':assists' => $homeAssists,
        ':rebounds' => $homeRebounds,
        ':steals' => $homeSteals,
        ':blocks' => $homeBlocks,
        ':turnovers' => $homeTurnovers,
        ':minutesPlayed' => $homeMinutesPlayed,
    ]);

    // Insert away player stats
    $stmt->execute([
        ':gameID' => $gameID,
        ':playerID' => $awayPlayerID,
        ':points' => $awayPoints,
        ':assists' => $awayAssists,
        ':rebounds' => $awayRebounds,
        ':steals' => $awaySteals,
        ':blocks' => $awayBlocks,
        ':turnovers' => $awayTurnovers,
        ':minutesPlayed' => $awayMinutesPlayed,
    ]);

    // Respond with success
    echo json_encode([
        'status' => 'success',
        'message' => 'Player stats submitted successfully.',
    ]);
} catch (Exception $e) {
    // Handle errors
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
?>
