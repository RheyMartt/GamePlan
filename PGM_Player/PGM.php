<?php
session_start(); // Start the session
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

// Ensure playerID is set in session
if (!isset($_SESSION['playerID'])) {
    echo "Player is not logged in!";
    exit;
}

$playerID = $_SESSION['playerID']; // Get the playerID from the session

// Fetch ongoing (not past) training sessions
function getOngoingTraining() {
    global $pdo, $playerID;

    try {
        $query = "SELECT 
                     p.firstName, 
                     p.lastName, 
                     tp.focusArea AS trainingPlan, 
                     DATE_FORMAT(t.trainingDate, '%Y-%m-%d') AS trainingDate, 
                     TIME_FORMAT(t.trainingTime, '%h:%i %p') AS trainingTime, 
                     TIME_FORMAT(ADDTIME(t.trainingTime, SEC_TO_TIME(tp.durationMinutes * 60)), '%h:%i %p') AS endTime
                  FROM training t
                  JOIN players p ON t.playerID = p.playerID
                  JOIN trainingPlans tp ON t.trainingPlanID = tp.trainingPlanID
                  WHERE t.trainingDate >= CURDATE() 
                  AND t.playerID = :playerID
                  ORDER BY t.trainingDate ASC, t.trainingTime ASC";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching ongoing training: " . $e->getMessage();
        return [];
    }
}

function getCompletedTrainings() {
    global $pdo, $playerID;

    try {
        $query = "SELECT 
                     p.firstName, 
                     p.lastName, 
                     tp.focusArea AS trainingPlan, 
                     t.trainingDate, 
                     t.trainingTime, 
                     ADDTIME(t.trainingTime, SEC_TO_TIME(tp.durationMinutes * 60)) AS endTime
                  FROM training t
                  JOIN players p ON t.playerID = p.playerID
                  JOIN trainingPlans tp ON t.trainingPlanID = tp.trainingPlanID
                  WHERE t.trainingDate < CURDATE()
                  AND t.playerID = :playerID
                  ORDER BY t.trainingDate DESC";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':playerID', $playerID, PDO::PARAM_INT);
        $stmt->execute();
        $completedTrainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total and completed training count for the logged-in player
        $countQuery = "SELECT 
                  (SELECT COUNT(*) FROM training WHERE playerID = :playerID1) AS totalTrainings, 
                  (SELECT COUNT(*) FROM training WHERE playerID = :playerID2 AND trainingDate < CURDATE()) AS completedTrainings";
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindParam(':playerID1', $playerID, PDO::PARAM_INT);
        $countStmt->bindParam(':playerID2', $playerID, PDO::PARAM_INT);
        $countStmt->execute();
        $progress = $countStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'trainings' => $completedTrainings,
            'progress' => $progress
        ];
    } catch (PDOException $e) {
        echo "Error fetching completed training: " . $e->getMessage();
        return [];
    }
}

function getCompletedTeamTrainings() {
    global $pdo;

    try {
        $query = "SELECT 
                     tp.focusArea AS trainingPlan, 
                     tt.trainingDate, 
                     tt.trainingTime, 
                     ADDTIME(tt.trainingTime, SEC_TO_TIME(tp.durationMinutes * 60)) AS endTime
                  FROM teamTraining tt
                  JOIN teamTrainingPlans tp ON tt.teamTrainingPlanID = tp.teamTrainingPlanID
                  WHERE tt.trainingDate < CURDATE() 
                  ORDER BY tt.trainingDate DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $completedTeamTrainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total and completed team training count
        $countQuery = "SELECT 
                          (SELECT COUNT(*) FROM teamTraining) AS totalTrainings, 
                          (SELECT COUNT(*) FROM teamTraining WHERE trainingDate < CURDATE()) AS completedTrainings";
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute();
        $progress = $countStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'trainings' => $completedTeamTrainings,
            'progress' => $progress
        ];
    } catch (PDOException $e) {
        echo "Error fetching completed team training: " . $e->getMessage();
        return [];
    }
}

// Fetch players from teamID = 1
function getTeamPlayers() {
    global $pdo;

    try {
        $query = "SELECT playerID, firstName, lastName FROM players WHERE teamID = 1 ORDER BY firstName";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(); // Fetch players
    } catch (PDOException $e) {
        echo "Error fetching players: " . $e->getMessage();
        return [];
    }
}

function getOngoingTeamTraining() {
    global $pdo;

    try {
        $query = "SELECT 
                     tp.focusArea AS trainingPlan, 
                     DATE_FORMAT(tt.trainingDate, '%Y-%m-%d') AS trainingDate, 
                     TIME_FORMAT(tt.trainingTime, '%h:%i %p') AS trainingTime, 
                     TIME_FORMAT(ADDTIME(tt.trainingTime, SEC_TO_TIME(tp.durationMinutes * 60)), '%h:%i %p') AS endTime
                  FROM teamTraining tt
                  JOIN teamTrainingPlans tp ON tt.teamTrainingPlanID = tp.teamTrainingPlanID
                  WHERE tt.trainingDate >= CURDATE()
                  ORDER BY tt.trainingDate ASC, tt.trainingTime ASC";

        $stmt = $pdo->prepare($query);  
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching ongoing team training: " . $e->getMessage();
        return [];
    }
}


$ongoingTrainings = getOngoingTraining();

$completedTrainingsData = getCompletedTrainings();
$completedTrainings = $completedTrainingsData['trainings'];
$playerProgress = $completedTrainingsData['progress'];

$players = getTeamPlayers();

$ongoingTeamTrainings = getOngoingTeamTraining();

$completedTeamTrainingsData = getCompletedTeamTrainings();
$completedTeamTrainings = $completedTeamTrainingsData['trainings'];
$teamProgress = $completedTeamTrainingsData['progress'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU GAMEPLAN</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="logo-container">
                <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="/gameplan/Player_Profile_Player/Player.phps" >PLAYER PROFILE</a></li>
                    <li><a href="/gameplan/Com/CommHub.html">TEAM COMMUNICATION</a></li>
                    <li><a href="/gameplan/Schedule_Player/PlayerSM.php">SCHEDULE</a></li>
                    <li><a href="/gameplan/PGM_Player/PGM.html" class="active">PROGRESS & MILESTONE</a></li>
                    <li><a href="/gameplan/Resource_Management_Player/RM.html">RESOURCES</a></li>
                    <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li> 
                </ul>
            </div>
        </div>

    <main>
   
        <section class="ongoing-training">
            <h2>ON GOING TRAINING</h2>
            <div class="players">
                <h3>Players</h3>
                <table>
                    <tr>
                        <th>Player Name</th>
                        <th>Training Plan</th>
                        <th>Start Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
                    <?php foreach ($ongoingTrainings as $training): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($training['firstName'] . ' ' . $training['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($training['trainingPlan']); ?></td>
                            <td><?php echo htmlspecialchars($training['trainingDate']); ?></td>
                            <td><?php echo htmlspecialchars($training['trainingTime']); ?></td>
                            <td><?php echo htmlspecialchars($training['endTime']); ?></td>
                            <td><i class="fas fa-eye"></i></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="team">
                <h3>Team</h3>
                <table>
                    <tr>
                        <th>Training Plan</th>
                        <th>Start Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
                    <?php foreach ($ongoingTeamTrainings as $teamTraining): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teamTraining['trainingPlan']); ?></td>
                            <td><?php echo htmlspecialchars($teamTraining['trainingDate']); ?></td>
                            <td><?php echo htmlspecialchars($teamTraining['trainingTime']); ?></td>
                            <td><?php echo htmlspecialchars($teamTraining['endTime']); ?></td>
                            <td><i class="fas fa-eye"></i></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>
        <section class="training-plan">
            <h2>TRAINING DONE</h2>
                <div class="training-container">
                <div class="players-training">
                    <h3>Players</h3>
                    <table>
                        <tr>
                            <th>Player Name</th>
                            <th>Training Plan</th>
                            <th>Training Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                        <?php foreach ($completedTrainings as $training): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($training['firstName'] . ' ' . $training['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingPlan']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingDate']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingTime']); ?></td>
                                <td><?php echo htmlspecialchars($training['endTime']); ?></td>
                                <td><i class="fas fa-eye"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="progress">
                        <?php echo "{$playerProgress['completedTrainings']}/{$playerProgress['totalTrainings']} DONE"; ?>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="team-training">
                    <h3>Team</h3>
                    <table>
                        <tr>
                            <th>Training Plan</th>
                            <th>Start Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                        <?php foreach ($completedTeamTrainings as $teamTraining): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teamTraining['trainingPlan']); ?></td>
                                <td><?php echo htmlspecialchars($teamTraining['trainingDate']); ?></td>
                                <td><?php echo htmlspecialchars($teamTraining['trainingTime']); ?></td>
                                <td><?php echo htmlspecialchars($teamTraining['endTime']); ?></td>
                                <td><i class="fas fa-eye"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="progress">
                        <?php echo "{$teamProgress['completedTrainings']}/{$teamProgress['totalTrainings']} DONE"; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
