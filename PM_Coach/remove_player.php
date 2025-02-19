<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playerID'])) {
    $playerID = $_POST['playerID'];

    try {
        $stmt = $pdo->prepare("DELETE FROM players WHERE playerID = :playerID");
        $stmt->execute([':playerID' => $playerID]);

        echo "success"; // Send success response
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Send error response
    }
}
?>
