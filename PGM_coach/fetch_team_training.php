<?php
include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; // Database connection

function getLatestTeamStats() { 
    global $pdo;

    $query = "SELECT p.playerID, p.firstName, p.lastName, p.position, p.teamID, gs.*
              FROM game_stats gs
              JOIN players p ON gs.playerID = p.playerID
              WHERE gs.gameID = (
                  SELECT g.gameID
                  FROM games g
                  JOIN game_stats gs ON g.gameID = gs.gameID
                  WHERE g.homeTeamID = 1 OR g.awayTeamID = 1
                  ORDER BY g.gameID DESC
                  LIMIT 1
              )
              AND p.teamID = 1 
              ORDER BY p.playerID ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all valid training focus areas from the database
function getValidFocusAreas() {
    global $pdo;
    $query = "SELECT focusArea FROM trainingPlans";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function identifyWeaknesses($stats, $validFocusAreas) {
    if (!isset($stats) || !is_array($stats)) {
        error_log("Error: stats is undefined or null in identifyWeaknesses()");
        return null; 
    }

    // Extract stats
    $position = $stats['position']; 
    $fgPercentage = $stats['fieldGoalsPercentage'] ?? 0;
    $fgAttempts = $stats['fieldGoalsAttempted'] ?? 0;
    $ftPercentage = $stats['freeThrowPercentage'] ?? 0;
    $ftAttempts = $stats['freeThrowsAttempted'] ?? 0;
    $turnovers = $stats['turnovers'] ?? 0;
    $assists = $stats['assists'] ?? 0;
    $rebounds = $stats['rebounds'] ?? 0;
    $steals = $stats['steals'] ?? 0;
    $blocks = $stats['blocks'] ?? 0;
    $minutesPlayed = $stats['minutesPlayed'] ?? 0;
    $points = $stats['points'] ?? 0;
    $plusMinus = $stats['plusMinus'] ?? 0;

    $weaknesses = [];

    // If player has LOW minutes, focus on development areas
    if ($minutesPlayed < 10) {
        if ($fgAttempts >= 5 && $fgPercentage < 30 && in_array('Shooting', $validFocusAreas)) {
            $weaknesses['Shooting'] = $fgPercentage;
        }
        if ($turnovers >= 5 && in_array('Ball Handling', $validFocusAreas)) {
            $weaknesses['Ball Handling'] = $turnovers;
        }
        if (in_array('Conditioning', $validFocusAreas)) {
            $weaknesses['Conditioning'] = $minutesPlayed; // Low minutes → Conditioning
        }
        return $weaknesses ? array_keys($weaknesses, min($weaknesses))[0] : null; 
    }

    // Shot Selection vs. Finishing
    if ($fgAttempts >= 5 && $fgPercentage < 40) {
        if ($points >= 10 && in_array('Shot Selection', $validFocusAreas)) {
            $weaknesses['Shot Selection'] = $fgPercentage;
        } elseif (in_array('Shooting', $validFocusAreas)) {
            $weaknesses['Shooting'] = $fgPercentage;
        }
    }

    // Free Throws
    if ($ftAttempts >= 3 && $ftPercentage < 70 && in_array('Free Throws', $validFocusAreas)) {
        $weaknesses['Free Throws'] = $ftPercentage;
    }

    // Passing (Contextualized)
    $assistToTurnoverRatio = ($turnovers > 0) ? $assists / $turnovers : $assists;
    if ($assists < 2 && $turnovers >= 3 && $assistToTurnoverRatio < 1 && in_array('Passing', $validFocusAreas)) {
        $weaknesses['Passing'] = $assists;
    }

    // Decision Making & Ball Handling
    if ($turnovers >= 4) {
        if ($turnovers > ($assists * 2) && in_array('Decision Making', $validFocusAreas)) {
            $weaknesses['Decision Making'] = $turnovers; // High turnovers & low assists = poor decisions
        }
        if (in_array('Ball Handling', $validFocusAreas)) {
            $weaknesses['Ball Handling'] = $turnovers;
        }
    }

    // Rebounding (Position-Specific)
    if ($rebounds < 3 && in_array($position, ['Center', 'Power Forward', 'Forward/Center']) && in_array('Rebounding', $validFocusAreas)) {
        $weaknesses['Rebounding'] = $rebounds;
    }

    // Defense
    if ($minutesPlayed > 10 && ($steals + $blocks < 1) && in_array('Defense', $validFocusAreas)) {
        $weaknesses['Defense'] = $steals + $blocks;
    }

    // Decision Making (Impact on Team)
    if ($minutesPlayed >= 11 && $plusMinus < -5 && in_array('Decision Making', $validFocusAreas)) {
        $weaknesses['Decision Making'] = $plusMinus;
    }

    // Final Check: If no weaknesses found, return the stat with the lowest value
    if (empty($weaknesses)) {
        $allStats = [
            'Shooting' => $fgPercentage,
            'Free Throws' => $ftPercentage,
            'Passing' => $assists,
            'Rebounding' => $rebounds,
            'Defense' => $steals + $blocks,
            'Decision Making' => $plusMinus,
            'Ball Handling' => $turnovers
        ];

        $filteredStats = array_intersect_key($allStats, array_flip($validFocusAreas));

        // Exclude Decision Making as a fallback to prevent mislabeling
        unset($filteredStats['Decision Making']);

        if (!empty($filteredStats)) {
            return array_keys($filteredStats, min($filteredStats))[0];
        }
        return null;
    }

    return array_keys($weaknesses, min($weaknesses))[0];
}


function getTrainingPlan($weakness) {
    global $pdo;
    $query = "SELECT trainingPlanID, focusArea FROM trainingPlans WHERE focusArea = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$weakness]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$validFocusAreas = getValidFocusAreas();
$players = getLatestTeamStats();
$trainingSuggestions = [];

foreach ($players as $player) {
    $weakness = identifyWeaknesses($player, $validFocusAreas);
    if ($weakness) {
        $trainingPlan = getTrainingPlan($weakness);
        if ($trainingPlan) {
            $trainingSuggestions[] = [
                "playerID" => $player['playerID'],
                "firstName" => $player['firstName'],
                "lastName" => $player['lastName'],
                "trainingPlan" => $trainingPlan['focusArea'],
                "gameStats" => [
                    "fieldGoalsPercentage" => $player['fieldGoalsPercentage'],
                    "fieldGoalsAttempted" => $player['fieldGoalsAttempted'],
                    "freeThrowPercentage" => $player['freeThrowPercentage'],
                    "freeThrowsAttempted" => $player['freeThrowsAttempted'],
                    "turnovers" => $player['turnovers'],
                    "assists" => $player['assists'],
                    "rebounds" => $player['rebounds'],
                    "steals" => $player['steals'],
                    "blocks" => $player['blocks'],
                    "minutesPlayed" => $player['minutesPlayed'],
                    "points" => $player['points'],
                    "plusMinus" => $player['plusMinus']
                ]
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($trainingSuggestions, JSON_PRETTY_PRINT);

?>
