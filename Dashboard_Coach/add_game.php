<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addGame"])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO games 
            (homeTeamID, awayTeamID, gameDate, gameTime, gameLocation, gameType, 
             homeQuarterOne, homeQuarterTwo, homeQuarterThree, homeQuarterFour, homeOvertime, homeFinalScore, 
             awayQuarterOne, awayQuarterTwo, awayQuarterThree, awayQuarterFour, awayOvertime, awayFinalScore) 
             VALUES 
            (:homeTeamID, :awayTeamID, :gameDate, :gameTime, :gameLocation, :gameType, 
             :homeQuarterOne, :homeQuarterTwo, :homeQuarterThree, :homeQuarterFour, :homeFinalScore, 
             :awayQuarterOne, :awayQuarterTwo, :awayQuarterThree, :awayQuarterFour, :awayFinalScore)");

        $stmt->execute([
            ':homeTeamID' => 1, // Your team is always home (TeamID = 1)
            ':awayTeamID' => $_POST['opponentID'],
            ':gameDate' => $_POST['gameDate'],
            ':gameTime' => $_POST['gameTime'],
            ':gameLocation' => $_POST['gameLocation'],
            ':gameType' => $_POST['gameType'],
            ':homeQuarterOne' => $_POST['homeQuarterOne'] ?? 0,
            ':homeQuarterTwo' => $_POST['homeQuarterTwo'] ?? 0,
            ':homeQuarterThree' => $_POST['homeQuarterThree'] ?? 0,
            ':homeQuarterFour' => $_POST['homeQuarterFour'] ?? 0,
            ':homeOvertime' => $_POST['homeOvertime'] ?? 0,
            ':homeFinalScore' => $_POST['homeFinalScore'] ?? 0,
            ':awayQuarterOne' => $_POST['awayQuarterOne'] ?? 0,
            ':awayQuarterTwo' => $_POST['awayQuarterTwo'] ?? 0,
            ':awayQuarterThree' => $_POST['awayQuarterThree'] ?? 0,
            ':awayQuarterFour' => $_POST['awayQuarterFour'] ?? 0,
            ':awayOvertime' => $_POST['awayOvertime'] ?? 0,
            ':awayFinalScore' => $_POST['awayFinalScore'] ?? 0
        ]);

        // Ensure correct JSON response
        header('Content-Type: application/json');
        echo json_encode(["status" => "success", "message" => "Game added successfully!"]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Error adding game: " . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
