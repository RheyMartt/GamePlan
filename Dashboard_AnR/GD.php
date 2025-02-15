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
  <style>
    .active {
        font-weight: bold;
    }
  </style>

    <!-- Navigation Bar -->
    <div class="navbar">
      <div class="logo-container">
        <img src="NU BULLDOG.png" alt="Logo" class="navbar-logo">
      </div>
      <div class="nav-links">
        <ul>
          <li><a href="#" class="active">GAME DASHBOARD</a></li>
          <li><a href="/gameplan/Com/CommHub.html">TEAM COMMUNICATION</a></li>
          <li><a href="/gameplan/PM_AnR/PM.html">PLAYER MANAGEMENT</a></li>
          <li><a href="/gameplan/Feedback_n_Analyzation_AnR/FnA.html">FEEDBACK & ANALYZATION</a></li>
          <li><a href="/gameplan/PGM_AnR/PGM.html">PROGRESS & MILESTONE</a></li>
          <li><a href="/gameplan/Resource_Management_AnR/RM.html">RESOURCES</a></li>
          <li><a href="#" title="Logout"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
      </div>
    </div>

    <!-- /gameplan/Dashboard_AnR/GD.php -->

    <main class="main-content">
      <!-- Dropdown menu for game selection -->
      <div class="dropdown" id="gameDropdown" style="text-align: right;">
        <button id="dropdownButton">NU vs. UST</button>
        <div class="dropdown-content">
          <a href="#" onclick="updateDropdown('NU vs. UST')">NU vs. UST</a>
          <a href="#" onclick="updateDropdown('NU vs. ADMU')">NU vs. ADMU</a>
          <a href="#" onclick="updateDropdown('NU vs. UP')">NU vs. UP</a>
          <a href="#" onclick="updateDropdown('NU vs. AdU')">NU vs. AdU</a>
          <a href="#" onclick="updateDropdown('NU vs. DLSU')">NU vs. DLSU</a>
          <a href="#" onclick="updateDropdown('NU vs. UE')">NU vs. UE</a>
          <a href="#" onclick="updateDropdown('NU vs. AdU')">NU vs. AdU</a>
        </div>
      </div>

      <!-- Add buttons above the court view -->
      <div class="court-buttons">
        <button onclick="changeCourtImage('court.jpg')">Court View</button>
        <button onclick="changeCourtImage('graph.jpg')">Graph View</button>
      </div>

      <div class="court-stats">
        <div class="court">
          <img src="court.jpg" alt="Court Image" class="court-image" id="courtImage">
        </div>

        <div class="game-details">
          <h1>
            <span>NATIONAL UNIVERSITY</span>
            <span>132 - 114</span>
            <span>UNIVERSITY OF STO. TOMAS</span>
          </h1>
          <p>Final</p>
          <p>National University vs. University of Sto. Tomas</p>
          <div class="score-breakdown">
            <div>1</div><div>2</div><div>3</div><div>4</div><div>T</div>
            <div>40</div><div>26</div><div>32</div><div>34</div><div>132</div>
            <div>30</div><div>31</div><div>27</div><div>26</div><div>114</div>
          </div>
        </div>

        <!-- Top Performers Section -->
        <div class="top-performers">
          <h3>Top Performers</h3>
          <div class="top-performers-list">
            <div class="performer">
              <p><strong>LeBron James</strong></p>
              <p>Points: 30 | Rebounds: 6 | Steals: 3 | Assists: 5</p>
            </div>
            <div class="performer">
              <p><strong>Stephen Curry</strong></p>
              <p>Points: 29 | Rebounds: 10 | Steals: 1 | Assists: 6</p>
            </div>
            <div class="performer">
              <p><strong>James Harden</strong></p>
              <p>Points: 26 | Rebounds: 8 | Steals: 2 | Assists: 5</p>
            </div>
            <div class="performer">
              <p><strong>Joel Embiid</strong></p>
              <p>Points: 25 | Rebounds: 12 | Steals: 2 | Assists: 7</p>
            </div>
            <div class="performer">
              <p><strong>Kawhi Leonard</strong></p>
              <p>Points: 24 | Rebounds: 12 | Steals: 3 | Assists: 8</p>
            </div>
          </div>
        </div>
      </div>

      <!-- NU Players Table -->
      <div class="player-stats-container">
        <div class="player-stats">
          <h2>NU Player Stats</h2>
          <table>
            <thead>
              <tr>
                <th>Player Name</th>
                <th>Team</th>
                <th>Pos</th>
                <th>Pts</th>
                <th>Asts</th>
                <th>Rbs</th>
                <th>Stls</th>
                <th>Blks</th>
                <th>TO</th>
                <th>MP</th>
                <th>FGM</th>
                <th>FGA</th>
                <th>FG%</th>
                <th>TPM</th>
                <th>TPA</th>
                <th>TP%</th>
                <th>FTM</th>
                <th>FTA</th>
                <th>FT%</th>
                <th>+/-</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>LeBron James</td>
                <td>NU</td>
                <td>F</td>
                <td>35</td>
                <td>10</td>
                <td>7</td>
                <td>5</td>
                <td>2</td>
                <td>2</td>
                <td>38</td>
                <td>14</td>
                <td>22</td>
                <td>63.6%</td>
                <td>4</td>
                <td>8</td>
                <td>50%</td>
                <td>3</td>
                <td>4</td>
                <td>75%</td>
                <td>+18</td>
              </tr>
              <tr>
                <td>Stephen Curry</td>
                <td>NU</td>
                <td>G</td>
                <td>32</td>
                <td>5</td>
                <td>3</td>
                <td>7</td>
                <td>0</td>
                <td>3</td>
                <td>36</td>
                <td>12</td>
                <td>21</td>
                <td>57.1%</td>
                <td>6</td>
                <td>11</td>
                <td>54.5%</td>
                <td>2</td>
                <td>2</td>
                <td>100%</td>
                <td>+15</td>
              </tr>
              <tr>
                <td>James Harden</td>
                <td>NU</td>
                <td>G</td>
                <td>28</td>
                <td>8</td>
                <td>5</td>
                <td>9</td>
                <td>1</td>
                <td>4</td>
                <td>40</td>
                <td>9</td>
                <td>18</td>
                <td>50%</td>
                <td>5</td>
                <td>10</td>
                <td>50%</</td>
                <td>5</td>
                <td>6</td>
                <td>83.3%</td>
                <td>+10</td>
              </tr>
              <tr>
                <td>Joel Embiid</td>
                <td>NU</td>
                <td>C</td>
                <td>30</td>
                <td>12</td>
                <td>2</td>
                <td>4</td>
                <td>3</td>
                <td>3</td>
                <td>39</td>
                <td>13</td>
                <td>24</td>
                <td>54.2%</td>
                <td>2</td>
                <td>5</td>
                <td>40%</td>
                <td>2</td>
                <td>3</td>
                <td>66.7%</td>
                <td>+14</td>
              </tr>
              <tr>
                <td>Victor Wembanyama</td>
                <td>NU</td>
                <td>C</td>
                <td>27</td>
                <td>11</td>
                <td>4</td>
                <td>3</td>
                <td>4</td>
                <td>2</td>
                <td>37</td>
                <td>11</td>
                <td>19</td>
                <td>57.9%</td>
                <td>3</td>
                <td>6</td>
                <td>50%</td>
                <td>4</td>
                <td>5</td>
                <td>80%</td>
                <td>+12</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- UST Players Table -->
      <div class="player-stats-container">
        <div class="player-stats">
          <h2>UST Player Stats</h2>
          <table>
            <thead>
              <tr>
                <th>Player Name</th>
                <th>Team</th>
                <th>Pos</th>
                <th>Pts</th>
                <th>Asts</th>
                <th>Rbs</th>
                <th>Stls</th>
                <th>Blks</th>
                <th>TO</th>
                <th>MP</th>
                <th>FGM</th>
                <th>FGA</th>
                <th>FG%</th>
                <th>TPM</th>
                <th>TPA</th>
                <th>TP%</th>
                <th>FTM</th>
                <th>FTA</th>
                <th>FT%</th>
                <th>+/-</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Kawhi Leonard</td>
                <td>UST</td>
                <td>F</td>
                <td>29</td>
                <td>11</td>
                <td>3</td>
                <td>7</td>
                <td>1</td>
                <td>2</td>
                <td>39</td>
                <td>12</td>
                <td>22</td>
                <td>54.5%</td>
                <td>3</td>
                <td>7</td>
                <td>42.9%</td>
                <td>2</td>
                <td>3</td>
                <td>66.7%</td>
                <td>+14</td>
              </tr>
              <tr>
                <td>Giannis Antetokounmpo</td>
                <td>UST</td>
                <td>F</td>
                <td>34</td>
                <td>16</td>
                <td>2</td>
                <td>6</td>
                <td>3</td>
                <td>4</td>
                <td>42</td>
                <td>14</td>
                <td>24</td>
                <td>58.3%</td>
                <td>2</td>
                <td>6</td>
                <td>33.3%</td>
                <td>4</td>
                <td>6</td>
                <td>66.7%</td>
                <td>+17</td>
              </tr>
              <tr>
                <td>Jimmy Butler</td>
                <td>UST</td>
                <td>G</td>
                <td>27</td>
                <td>7</td>
                <td>2</td>
                <td>8</td>
                <td>1</td>
                <td>3</td>
                <td>40</td>
                <td>10</td>
                <td>20</td>
                <td>50%</td>
                <td>2</td>
                <td>5</td>
                <td>40%</td>
                <td>5</td>
                <td>6</td>
                <td>83.3%</td>
                <td>+12</td>
              </tr>
              <tr>
                <td>Devin Booker</td>
                <td>UST</td>
                <td>G</td>
                <td>36</td>
                <td>5</td>
                <td>1</td>
                <td>6</td>
                <td>0</td>
                <td>2</td>
                <td>38</td>
                <td>13</td>
                <td>26</td>
                <td>50%</td>
                <td>4</td>
                <td>9</td>
                <td>44.4%</td>
                <td>6</td>
                <td>7</td>
                <td>85.7%</td>
                <td>+15</td>
              </tr>
              <tr>
                <td>Trae Young</td>
                <td>UST</td>
                <td>G</td>
                <td>30</td>
                <td>4</td>
                <td>1</td>
                <td>11</td>
                <td>0</td>
                <td>3</td>
                <td>39</</td>
                <td>11</td>
                <td>24</td>
                <td>45.8%</td>
                <td>5</td>
                <td>12</td>
                <td>41.7%</td>
                <td>3</td>
                <td>4</td>
                <td>75%</td>
                <td>+13</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Link to the external JavaScript file -->
  <script src="script.js"></script>
</body>
</html>
