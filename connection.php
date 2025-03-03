<?php
$host = 'localhost'; // Hostname or IP address
$db = 'nu_game_plan'; // Database name
$user = 'root'; // MySQL username
$port = 3306; // MySQL port
$pass = ''; // MySQL password
$charset = 'utf8mb4'; // Character set (optional but recommended)

try {
    // Set DSN (Data Source Name)
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    
    // Options for PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enables exceptions for errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetches results as associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Disables emulated prepared statements
    ];
    
    // Create a PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>