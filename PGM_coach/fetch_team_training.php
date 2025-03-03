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
              AND p.teamID = 1";

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
    $position = $stats['position']; 
    $fgPercentage = $stats['fieldGoalsPercentage'] ?? 0;
    $fgAttempts = $stats['fieldGoalsAttempted'] ?? 0;
    $ftPercentage = $stats['freeThrowPercentage'] ?? 0;
    $ftAttempts = $stats['freeThrowsAttempted'] ?? 0;
    $turnovers = $stats['turnovers'] ?? 0;
    $assists = $stats['assists'] ?? 0;
    $rebounds = $stats['rebounds'] ?? 0;
    $offRebounds = $stats['offensiveRebounds'] ?? 0;
    $defRebounds = $stats['defensiveRebounds'] ?? 0;
    $steals = $stats['steals'] ?? 0;
    $blocks = $stats['blocks'] ?? 0;
    $minutesPlayed = $stats['minutesPlayed'] ?? 0;
    $points = $stats['points'] ?? 0;
    $plusMinus = $stats['plusMinus'] ?? 0;

    $weaknesses = [];

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

    // Passing & Ball Handling
    if ($assists < 2 && $turnovers >= 3 && in_array('Passing', $validFocusAreas)) {
        $weaknesses['Passing'] = $assists;
    }
    if ($turnovers > ($assists * 1.5) && $turnovers >= 3 && in_array('Decision Making', $validFocusAreas)) {
        $weaknesses['Decision Making'] = $turnovers;
    }
    if ($turnovers >= 4 && in_array('Ball Handling', $validFocusAreas)) {
        $weaknesses['Ball Handling'] = $turnovers;
    }

    // Rebounding
    if ($rebounds < 3 && in_array($position, ['Center', 'Power Forward', 'Forward/Center']) && in_array('Rebounding', $validFocusAreas)) {
        $weaknesses['Rebounding'] = $rebounds;
    }

    // Defense
    if ($minutesPlayed > 10 && ($steals + $blocks < 1) && in_array('Defense', $validFocusAreas)) {
        $weaknesses['Defense'] = $steals + $blocks;
    }

    // Decision Making
    if ($minutesPlayed >= 15 && $plusMinus < -5 && in_array('Decision Making', $validFocusAreas)) {
        $weaknesses['Decision Making'] = $plusMinus;
    }

    // Conditioning
    if ($minutesPlayed > 0 && $minutesPlayed < 10 && in_array('Conditioning', $validFocusAreas)) {
        $weaknesses['Conditioning'] = $minutesPlayed;
    }

    // If no weaknesses match the database, pick the lowest available stat
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

        // Filter stats to match only valid training areas
        $filteredStats = array_intersect_key($allStats, array_flip($validFocusAreas));

        if (!empty($filteredStats)) {
            return array_keys($filteredStats, min($filteredStats))[0];
        }
        return null; // No valid weakness found
    }

    // Return the weakest identified focus area
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
                "trainingPlan" => $trainingPlan['focusArea']
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($trainingSuggestions, JSON_PRETTY_PRINT);
?>
