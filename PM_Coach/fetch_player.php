<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GamePlan/connection.php';

if (isset($_POST['playerID'])) {
    $playerID = $_POST['playerID'];

    try {
        $stmt = $pdo->prepare("SELECT firstName, lastName, position, status, height, weight FROM players WHERE playerID = :playerID");
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->execute();
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($player) {
            echo json_encode($player);
        } else {
            echo json_encode(["error" => "Player not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
