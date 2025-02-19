<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // connection filepath

header('Content-Type: application/json');

// Validate input
if (!isset($_POST['playerID']) || empty($_POST['playerID'])) {
    echo json_encode(['error' => 'Invalid player ID']);
    exit;
}

$playerID = $_POST['playerID'];

try {
    // Fetch attendance stats
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT sessionID) AS total_sessions,
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS attended_sessions,
            SUM(CASE WHEN status IN ('Absent', 'Excused') THEN 1 ELSE 0 END) AS missed_sessions,
            MAX(sessionID) AS last_attendance_session
        FROM attendance 
        WHERE playerID = :playerID
    ");
    $stmt->execute(['playerID' => $playerID]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    $lastAttendanceDate = "No record";
    if ($attendance['last_attendance_session']) {
        $stmt = $pdo->prepare("SELECT sessionType, sessionID FROM attendance WHERE sessionID = :sessionID LIMIT 1");
        $stmt->execute(['sessionID' => $attendance['last_attendance_session']]);
        $lastSession = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastSession) {
            $lastAttendanceDate = "Session " . $lastSession['sessionID'] . " (" . $lastSession['sessionType'] . ")";
        }
    }

    echo json_encode([
        'total_sessions' => $attendance['total_sessions'] ?? 0,
        'attended_sessions' => $attendance['attended_sessions'] ?? 0,
        'missed_sessions' => $attendance['missed_sessions'] ?? 0,
        'lastAttendanceDate' => $lastAttendanceDate
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
