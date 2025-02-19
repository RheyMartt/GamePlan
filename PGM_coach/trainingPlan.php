<?php 
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

// Function to get the latest team stats
function getLatestTeamStats($pdo) {
    try {
        $query = "SELECT p.playerID, p.firstName, p.lastName, p.position, p.teamID, gs.*
                  FROM game_stats gs
                  JOIN players p ON gs.playerID = p.playerID
                  WHERE gs.gameID = (
                      SELECT gs.gameID
                      FROM game_stats gs
                      JOIN games g ON gs.gameID = g.gameID
                      WHERE g.homeTeamID = 1 OR g.awayTeamID = 1
                      ORDER BY gs.gameID DESC
                      LIMIT 1
                  )
                  AND p.teamID = 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stats) {
            return ['success' => true, 'teamStats' => $stats];
        } else {
            return ['success' => false, 'message' => 'No stats found for the latest game.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching team stats: ' . $e->getMessage()];
    }
}

// Function to suggest a team training plan based on performance
function suggestTeamTrainingPlan($pdo) {
    try {
        $query = "SELECT 
                    AVG(gs.fieldGoalsPercentage) AS avgFG,
                    AVG(gs.threePointsPercentage) AS avg3PT,
                    AVG(gs.freeThrowPercentage) AS avgFT,
                    AVG(gs.assists) AS avgAssists,
                    AVG(gs.turnovers) AS avgTurnovers,
                    AVG(gs.steals) AS avgSteals,
                    AVG(gs.blocks) AS avgBlocks,
                    AVG(gs.rebounds) AS avgRebounds
                  FROM game_stats gs
                  JOIN players p ON gs.playerID = p.playerID
                  WHERE gs.gameID = (
                      SELECT gs.gameID
                      FROM game_stats gs
                      JOIN games g ON gs.gameID = g.gameID
                      WHERE g.homeTeamID = 1 OR g.awayTeamID = 1
                      ORDER BY gs.gameID DESC
                      LIMIT 1
                  )
                  AND p.teamID = 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stats) {
            return ['success' => false, 'message' => 'No team stats available.'];
        }

        // Determine training focus based on performance
        if ($stats['avgFG'] < 40 || $stats['avgAssists'] < 15) {
            $trainingPlan = 'Offensive Strategies';
        } elseif ($stats['avgSteals'] < 5 || $stats['avgBlocks'] < 3) {
            $trainingPlan = 'Defensive Tactics';
        } elseif ($stats['avgTurnovers'] > 15) {
            $trainingPlan = 'Speed & Agility';
        } elseif ($stats['avgRebounds'] < 30) {
            $trainingPlan = 'Strength & Conditioning';
        } else {
            $trainingPlan = 'General Team Training'; // Default if no criteria match
        }

        return ['success' => true, 'trainingPlan' => $trainingPlan];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error analyzing team performance: ' . $e->getMessage()];
    }
}

header('Content-Type: application/json');
echo json_encode(suggestTeamTrainingPlan($pdo));

