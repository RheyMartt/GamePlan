<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playerID = $_POST['playerID'];

    try {
        $stmt = $pdo->prepare("UPDATE players SET status = 'Active', injuryType = NULL, injuryDate = NULL WHERE playerID = :playerID");
        $stmt->execute([':playerID' => $playerID]);

        echo json_encode(["success" => true, "message" => "Player status reverted to active."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>
