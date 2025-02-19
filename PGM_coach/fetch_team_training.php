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

function identifyWeaknesses($stats) {
    $position = $stats['position']; 
    $fgPercentage = $stats['fieldGoalsPercentage'] ?? 0;
    $fgAttempts = $stats['fieldGoalsAttempted'] ?? 0;
    $ftPercentage = $stats['freeThrowPercentage'] ?? 0;
    $turnovers = $stats['turnovers'] ?? 0;
    $assists = $stats['assists'] ?? 0;
    $rebounds = $stats['rebounds'] ?? 0;
    $steals = $stats['steals'] ?? 0;
    $blocks = $stats['blocks'] ?? 0;
    $minutesPlayed = $stats['minutesPlayed'] ?? 0;
    $points = $stats['points'] ?? 0;

    $weaknesses = [];

    if ($fgAttempts > 0 && $fgPercentage < 40) $weaknesses['Shooting'] = $fgPercentage;
    if ($turnovers > 3) $weaknesses['Ball Handling'] = $turnovers;
    if ($assists < 2) $weaknesses['Passing'] = $assists;
    if ($rebounds < 5) $weaknesses['Rebounding'] = $rebounds;
    if ($steals < 1 && $blocks < 1) $weaknesses['Defense'] = min($steals, $blocks);
    if ($ftPercentage < 70) $weaknesses['Free Throws'] = $ftPercentage;
    if ($points < 8 && $fgPercentage < 45) $weaknesses['Finishing'] = $points;
    if ($minutesPlayed < 10) $weaknesses['Conditioning'] = $minutesPlayed;

    if (in_array($position, ['Guard', 'Point Guard', 'Shooting Guard'])) {
        $guardStats = [
            'Scoring' => $points,
            'Rebounding' => $rebounds,
            'Passing' => $assists
        ];
        asort($guardStats);
        $weakestCategory = key($guardStats);

        switch ($weakestCategory) {
            case 'Scoring': $weaknesses['Shooting'] = $points; break;
            case 'Rebounding': $weaknesses['Rebounding'] = $rebounds; break;
            case 'Passing': $weaknesses['Passing'] = $assists; break;
        }
    }

    if (in_array($position, ['Small Forward', 'Power Forward', 'Forward'])) {
        if ($rebounds < 5) $weaknesses['Rebounding'] = $rebounds;
        if ($steals < 1 && $blocks < 1) $weaknesses['Defense'] = min($steals, $blocks);
    }

    if ($position === 'Center') {
        if ($rebounds < 5) $weaknesses['Rebounding'] = $rebounds;
        if ($ftPercentage < 70) $weaknesses['Free Throws'] = $ftPercentage;
    }

    asort($weaknesses);
    return array_key_first($weaknesses) ?: null;
}


function getTrainingPlan($weakness) {
    global $pdo;

    $query = "SELECT trainingPlanID, focusArea FROM trainingPlans WHERE focusArea = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$weakness]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$players = getLatestTeamStats();
$trainingSuggestions = [];

foreach ($players as $player) {
    $weakness = identifyWeaknesses($player);

    if ($weakness) { // Ensure there is a weakness before proceeding
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
echo json_encode($trainingSuggestions);
?>
