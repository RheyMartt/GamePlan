<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection
session_start(); // Start the session

// Fetch ongoing (not past) training sessions
function getOngoingTraining() {
    global $pdo;

    try {
        $query = "SELECT p.firstName, p.lastName, tp.focusArea AS trainingPlan, 
                         DATE_FORMAT(t.trainingDate, '%Y-%m-%d') AS trainingDate, 
                         TIME_FORMAT(t.trainingTime, '%h:%i %p') AS trainingTime
                  FROM training t
                  JOIN players p ON t.playerID = p.playerID
                  JOIN trainingPlans tp ON t.trainingPlanID = tp.trainingPlanID
                  WHERE t.trainingDate >= CURDATE()  -- Get today & future
                  ORDER BY t.trainingDate ASC, t.trainingTime ASC"; // Order by earliest first

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching ongoing training: " . $e->getMessage();
        return [];
    }
}

// Fetch training sessions
$ongoingTrainings = getOngoingTraining();
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
    <header>
        <div class="navbar">
            <div class="logo-container">
                <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="/gameplan/Dashboard_Coach/GD.php" >GAME DASHBOARD</a></li>
                    <li><a href="#">TEAM COMMUNICATION</a></li>
                    <li><a href="/gameplan/PM_Coach/PM.html">PLAYER MANAGEMENT</a></li>
                    <li><a href="/gameplan/Schedule_Coach/SM.html">SCHEDULE</a></li>
                    <li><a href="#" class="active">PROGRESS AND MILESTONE</a></li>
                    <li><a href="/gameplan/Resource_Management_Coach/RM.html">RESOURCES</a></li>
                    <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <section class="add-training">
            <h2>ADD A TRAINING PLAN</h2>
            <div class="toggle">
                <select>
                    <option selected disabled>Select Player</option>
                    <option>Player 1</option>
                    <option>Player 2</option>
                    <option>Player 3</option>
                    <option>Player 4</option>
                    <option>Player 5</option>
                    <option>Player 6</option>
                    <option>Player 7</option>
                    <option>Player 8</option>
                    <option>Player 9</option>
                    <option>Player 10</option>
                    <option>Player 11</option>
                    <option>Player 12</option>
                    <option>Player 13</option>
                    <option>Player 14</option>
                    <option>Player 15</option>
                </select>
                <button>TEAM</button>
            </div>
            <form>
                <label>TRAINING PLAN:</label>
                <input type="text">
                <label>START DATE:</label>
                <input type="date">
                <label>END DATE:</label>
                <input type="date">
                <button type="submit">ADD</button>
            </form>
        </section>
        <section class="ongoing-training">
            <h2>ON GOING TRAINING</h2>
            <div class="players">
                <h3>Players</h3>
                <table>
                    <tr>
                        <th>Player</th>
                        <th>Training Plan</th>
                        <th>Start Date</th>
                        <th>End Time</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
                    <?php if (!empty($ongoingTrainings)): ?>
                        <?php foreach ($ongoingTrainings as $training): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($training['firstName'] . ' ' . $training['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingPlan']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingDate']); ?></td>
                                <td><?php echo htmlspecialchars($training['trainingTime']); ?></td>
                                <td><i class="fas fa-eye"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No ongoing training found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>                         
            <div class="team">
                <h3>Team</h3>
                <table>
                    <tr>
                        <th>Training Plan</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
                    <tr>
                        <th>Training Plan</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
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
                            <th>Player 1</th>
                            <th>Training Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                        <tr>
                            <th>Player 2</th>
                            <th>Training Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                    </table>
                    <div class="progress">8/10 DONE</div>
                </div>
                <div class="divider"></div>
                <div class="team-training">
                    <h3>Team</h3>
                    <table>
                        <tr>
                            <th>Training Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                        <tr>
                            <th>Training Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th><i class="fas fa-eye"></i></th>
                        </tr>
                    </table>
                    <div class="progress">3/7 DONE</div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
