<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addStats'])) {
    $playerID = $_POST['playerID'];
    $gameID = $_POST['gameID']; // Ensure you pass the game ID from the previous form
    $points = $_POST['points'];
    $assists = $_POST['assists'];
    $rebounds = $_POST['rebounds'];
    $steals = $_POST['steals'];
    $blocks = $_POST['blocks'];
    $turnovers = $_POST['turnovers'];
    $minutesPlayed = $_POST['minutesPlayed'];

    try {
        $stmt = $pdo->prepare("INSERT INTO playerstats (gameID, playerID, points, assists, rebounds, steals, blocks, turnovers, minutesPlayed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gameID, $playerID, $points, $assists, $rebounds, $steals, $blocks, $turnovers, $minutesPlayed]);
        
        echo "Player stats added successfully!";
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
