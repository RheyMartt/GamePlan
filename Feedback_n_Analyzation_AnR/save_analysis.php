<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gameID = $_POST["gameID"];
    $findings = $_POST["findings"];
    $conclusion = $_POST["conclusion"];
    $keyFindings = $_POST["keyFindings"];

    if (empty($gameID) || empty($findings) || empty($conclusion) || empty($keyFindings)) {
        echo "All fields are required.";
        exit;
    }

    try {
        // Fetch homeTeamID and awayTeamID based on the selected game
        $stmt = $pdo->prepare("SELECT homeTeamID, awayTeamID FROM games WHERE gameID = :gameID");
        $stmt->execute([":gameID" => $gameID]);
        $teams = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teams) {
            echo "Invalid game selection.";
            exit;
        }

        $homeTeamID = $teams['homeTeamID'];
        $awayTeamID = $teams['awayTeamID'];

        // Insert data into game_analysis table
        $sql = "INSERT INTO game_analysis (gameID, homeTeamID, awayTeamID, findings, conclusion, keyFindings) 
                VALUES (:gameID, :homeTeamID, :awayTeamID, :findings, :conclusion, :keyFindings)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":gameID" => $gameID,
            ":homeTeamID" => $homeTeamID,
            ":awayTeamID" => $awayTeamID,
            ":findings" => $findings,
            ":conclusion" => $conclusion,
            ":keyFindings" => $keyFindings
        ]);

        echo "Game analysis saved successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
