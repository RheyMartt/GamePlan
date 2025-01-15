<?php 

include 'C:\\xampp\\htdocs\\GamePlan\\connection.php'; //connection filepath

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="player-profile">
        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="logo-container">
                <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>
                    <li><a href="#">Title Here</a></li>


                </ul>
            </div>
        </div>

        <!-- Player Profile Section -->
        <section class="profile-section">
            <div class="profile-header">
                <img src="LBJ.png" alt="Player Silhouette" class="player-image">
                <div class="player-info">
                    <h4>NU BULLDOGS</h4>
                    <p># 23 | Forward</p>
                    <h2>Lebron James</h2>
                </div>
                <img src="Bulldog.png" alt="Team Logo" class="team-logo">
            </div>
        
            <div class="player-details">
                <div class="stats">
                    <div class="stat">PPG <span>23.8</span></div>
                    <div class="stat">RBG <span>7.7</span></div>
                    <div class="stat">APG <span>8.8</span></div>
                </div>
        
                <div class="additional-info">
                    <div class="info-group">Height: <span>6'9"</span></div>
                    <div class="info-group">Weight: <span>250lb</span></div>
                    <div class="info-group">Age: <span>23 years</span></div>
                    <div class="info-group">Birthdate: <span>December 30, 1984</span></div>
                    <div class="info-group">Province: <span>Puerto Princessa, Palawan</span></div>
                </div>
            </div>
        </section>

      <!-- Career Highlights Section -->
<section class="career-highlights">
    <h3>Career Highlights</h3>
    <div class="highlights-content">
        <!-- Awards and Honors -->
        <div class="highlight-group">
            <h4>Awards and Honors</h4>
            <ul>
                <li>Most Valuable Player (MVP) - 2022</li>
                <li>All-Star Selection - 3 Times</li>
                <li>Defensive Player of the Year - 2021</li>
            </ul>
        </div>

        <!-- Team Achievements -->
        <div class="highlight-group">
            <h4>Team Achievements</h4>
            <ul>
                <li>National Champions - 2022</li>
                <li>Regional Champions - 2021, 2023</li>
                <li>Best Offensive Team - 2022</li>
            </ul>
        </div>

        <!-- Personal Records -->
        <div class="highlight-group">
            <h4>Personal Records</h4>
            <ul>
                <li>Career-High Points in a Game: 55</li>
                <li>Triple-Doubles: 25</li>
                <li>Consecutive Games Scoring 30+: 10</li>
            </ul>
        </div>
    </div>
</section>

       <!-- More From the Roster Section -->
<section class="roster">
    <h3>More From the Roster</h3>
    <div class="roster-list">
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#7 | Center - Forward</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#6 | Forward</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#5 | Guard</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#8 | Guard - Forward</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#9 | Forward</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#10 | Guard</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#11 | Center</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#12 | Forward - Center</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#13 | Guard</span>
            <span>PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#14 | Guard - Forward</span>
            <span>PLAYER NAME</span>
        </div>
        <!-- Added three new players -->
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#15 | Forward</span>
            <span>NEW PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#16 | Center</span>
            <span>NEW PLAYER NAME</span>
        </div>
        <div class="roster-item">
            <img src="Vacant Player.png" alt="Player">
            <span>#17 | Guard</span>
            <span>NEW PLAYER NAME</span>
        </div>
    </div>
</section>
    </div>
</body>
</html>
