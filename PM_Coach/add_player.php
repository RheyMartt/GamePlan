<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $position = $_POST['position'];
    $jerseyNumber = $_POST['jerseyNumber'];
    $height = $_POST['height'] ?? NULL;
    $weight = $_POST['weight'] ?? NULL;
    $birthdate = $_POST['birthdate'];
    $teamID = 1; // Always set to 1
    $status = "Active"; // Default status

    try {
        $stmt = $pdo->prepare("INSERT INTO players (firstName, lastName, position, jerseyNumber, height, weight, birthdate, status, teamID) 
                               VALUES (:firstName, :lastName, :position, :jerseyNumber, :height, :weight, :birthdate, :status, :teamID)");
        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':position' => $position,
            ':jerseyNumber' => $jerseyNumber,
            ':height' => $height,
            ':weight' => $weight,
            ':birthdate' => $birthdate,
            ':status' => $status,
            ':teamID' => $teamID
        ]);

        echo "success";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
