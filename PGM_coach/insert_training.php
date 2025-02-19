<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['trainings'])) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($data['trainings'] as $training) {
        // Get playerID
        $playerQuery = "SELECT playerID FROM players WHERE CONCAT(firstName, ' ', lastName) = :playerName";
        $stmt = $pdo->prepare($playerQuery);
        $stmt->execute(['playerName' => $training['playerName']]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$player) {
            throw new Exception("Player not found: " . $training['playerName']);
        }

        // Get trainingPlanID
        $planQuery = "SELECT trainingPlanID FROM trainingPlans WHERE focusArea = :trainingPlan";
        $stmt = $pdo->prepare($planQuery);
        $stmt->execute(['trainingPlan' => $training['trainingPlan']]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            throw new Exception("Training plan not found: " . $training['trainingPlan']);
        }

        // Insert into training table
        $insertQuery = "INSERT INTO training (trainingPlanID, playerID, trainingDate, trainingTime) 
                        VALUES (:trainingPlanID, :playerID, :trainingDate, :trainingTime)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            'trainingPlanID' => $plan['trainingPlanID'],
            'playerID' => $player['playerID'],
            'trainingDate' => $training['startDate'],
            'trainingTime' => $training['startTime']
        ]);
    }

    $pdo->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
