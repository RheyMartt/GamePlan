<?php
include 'C:\xampp\htdocs\GamePlan\connection.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ensure game details exist
    if (!isset($_POST['opponentID'], $_POST['gameDate'], $_POST['gameTime'], $_POST['gameLocation'], $_POST['gameType'])) {
        die("Error: Missing game details.");
    }

    $opponentID = $_POST['opponentID'];
    $gameDate = $_POST['gameDate'];
    $gameTime = $_POST['gameTime'];
    $gameLocation = $_POST['gameLocation'];
    $gameType = $_POST['gameType'];

    // Ensure file is uploaded
    if (!isset($_FILES['csvFile']['tmp_name']) || empty($_FILES['csvFile']['tmp_name'])) {
        die("File upload failed. Try again.");
    }

    $fileTmpPath = $_FILES['csvFile']['tmp_name'];
    $file = fopen($fileTmpPath, 'r');

    if (!$file) {
        die("Error opening file.");
    }

    // Skip headers
    fgetcsv($file); // Skip first row (date)
    fgetcsv($file); // Skip second row (empty)

    // Read game details (Quarter scores & OT)
    fgetcsv($file); // Skip empty line before team name
    $homeRow = fgetcsv($file); // NU Bulldogs Scores

    fgetcsv($file); // Skip "UST Growling Tigers" team name
    fgetcsv($file); // Skip "Q1, Q2, Q3, Q4, OT, FINAL" header row
    $awayRow = fgetcsv($file); // Read UST Growling Tigers Scores

    // Debugging output - print extracted rows
    //echo "<pre>";
    //echo "NU Bulldogs Scores: "; print_r($homeRow);
    //echo "UST Growling Tigers Scores: "; print_r($awayRow);
    //echo "</pre>";

    // Extract scores safely (avoid missing values)
    $homeQ1 = $homeRow[0] ?? 0;
    $homeQ2 = $homeRow[1] ?? 0;
    $homeQ3 = $homeRow[2] ?? 0;
    $homeQ4 = $homeRow[3] ?? 0;
    $homeOT = $homeRow[4] ?? 0; // Ensure this is not missing
    $homeFinal = $homeRow[5] ?? 0;

    $awayQ1 = $awayRow[0] ?? 0;
    $awayQ2 = $awayRow[1] ?? 0;
    $awayQ3 = $awayRow[2] ?? 0;
    $awayQ4 = $awayRow[3] ?? 0;
    $awayOT = $awayRow[4] ?? 0; // Ensure this is not missing
    $awayFinal = $awayRow[5] ?? 0;

    // Insert new game (including manual inputs)
    //echo "<pre>";
    //print_r([$gameDate, $gameTime, $gameLocation, $gameType, 
        //$homeQ1, $homeQ2, $homeQ3, $homeQ4, $homeOT, $homeFinal, 
        //$awayQ1, $awayQ2, $awayQ3, $awayQ4, $awayOT, $awayFinal]);
    //echo "</pre>";
    
    

    $stmt = $pdo->prepare("INSERT INTO games (homeTeamID, awayTeamID, gameDate, gameTime, gameLocation, gameType, 
        homeQuarterOne, homeQuarterTwo, homeQuarterThree, homeQuarterFour, homeOvertime, homeFinalScore, 
        awayQuarterOne, awayQuarterTwo, awayQuarterThree, awayQuarterFour, awayOvertime, awayFinalScore)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([1, $opponentID, $gameDate, $gameTime, $gameLocation, $gameType,
        $homeQ1, $homeQ2, $homeQ3, $homeQ4, $homeOT, $homeFinal, 
        $awayQ1, $awayQ2, $awayQ3, $awayQ4, $awayOT, $awayFinal]);

    $gameID = $pdo->lastInsertId();

    // Skip empty rows before player stats
    fgetcsv($file);
    fgetcsv($file);

    // Process player stats
    while (($data = fgetcsv($file)) !== FALSE) {
        if (empty($data[0]) || $data[0] == "player" || $data[0] == "NU Bulldogs" || $data[0] == "UST Growling Tigers") {
            continue;
        }

        // Get playerID
        $stmt = $pdo->prepare("SELECT playerID FROM players WHERE CONCAT(firstName, ' ', lastName) = ?");
        $stmt->execute([$data[0]]);
        $playerID = $stmt->fetchColumn();

        if ($playerID) {
            // Extract stats from CSV row
            list($playerName, $points, $rebounds, $assists, $steals, $blocks, $turnovers, $minutesPlayed, 
            $fgMade, $fgAttempted, $threePM, $threePA, $ftMade, $ftAttempted, $plusMinus) = array_pad($data, 15, 0);

            // Calculate percentages
            $fieldGoalPercentage = ($fgAttempted > 0) ? ($fgMade / $fgAttempted) * 100 : 0;
            $threePointPercentage = ($threePA > 0) ? ($threePM / $threePA) * 100 : 0;
            $freeThrowPercentage = ($ftAttempted > 0) ? ($ftMade / $ftAttempted) * 100 : 0;

            // Insert player stats into game_stats table
            $stmt = $pdo->prepare("INSERT INTO game_stats (gameID, playerID, points, rebounds, assists, steals, blocks, turnovers, 
            minutesPlayed, fieldGoalsMade, fieldGoalsAttempted, fieldGoalsPercentage, 
            threePointersMade, threePointersAttempted, threePointsPercentage, 
            freeThrowsMade, freeThrowsAttempted, freeThrowPercentage, plusMinus) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$gameID, $playerID, $points, $rebounds, $assists, $steals, $blocks, $turnovers, 
            $minutesPlayed, $fgMade, $fgAttempted, $fieldGoalPercentage, 
            $threePM, $threePA, $threePointPercentage, 
            $ftMade, $ftAttempted, $freeThrowPercentage, $plusMinus]);

        }
    }

    fclose($file);
    echo "Game and stats successfully uploaded.";
}
?>
