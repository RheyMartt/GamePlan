<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playerID = $_POST['playerID'];
    $injuryType = $_POST['injuryType'];
    $injuryDate = $_POST['injuryDate'];

    try {
        $stmt = $pdo->prepare("UPDATE players SET status = 'Injured', injuryType = :injuryType, injuryDate = :injuryDate WHERE playerID = :playerID");
        $stmt->execute([
            ':injuryType' => $injuryType,
            ':injuryDate' => $injuryDate,
            ':playerID' => $playerID
        ]);

        echo json_encode(["success" => true, "message" => "Player classified as injured."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>
