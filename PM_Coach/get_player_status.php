<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php';

$playerID = $_GET['playerID'];

try {
    $stmt = $pdo->prepare("SELECT status FROM players WHERE playerID = :playerID");
    $stmt->execute([':playerID' => $playerID]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($player);
} catch (PDOException $e) {
    echo json_encode(["status" => "Error", "message" => $e->getMessage()]);
}
?>
