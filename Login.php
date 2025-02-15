<?php
include 'connection.php'; // Connection file name
session_start(); // Start the session

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
        exit;
    }

    // Fetch user credentials from the database based on role
    $stmt = $pdo->prepare("SELECT uc.username, uc.password, uc.role, 
                            p.playerID, p.firstName, p.lastName, p.position,
                            p.jerseyNumber, p.height, p.weight, p.birthdate,
                            c.coachID, a.analystID
                        FROM user_credentials uc
                        LEFT JOIN players p ON uc.referenceID = p.playerID AND uc.role = 'Player'
                        LEFT JOIN coaches c ON uc.referenceID = c.coachID AND uc.role = 'Coach'
                        LEFT JOIN analysts a ON uc.referenceID = a.analystID AND uc.role = 'Analyst'
                        WHERE uc.username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        // Compare the plain text password directly
        if ($password === $user['password']) {
            // Store relevant user data in session
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'Player') {
                $_SESSION['playerID'] = $user['playerID'];
                echo json_encode([
                    'success' => true,
                    'playerID' => $user['playerID'],
                    'playerName' => $user['firstName'] . ' ' . $user['lastName'],
                    'role' => $user['role'],
                    'playerPosition' => $user['position'],
                    'jerseyNumber' => $user['jerseyNumber'],
                    'height' => $user['height'],
                    'weight' => $user['weight'],
                    'birthdate' => $user['birthdate'],
                ]);
            } elseif ($user['role'] == 'Coach') {
                $_SESSION['coachID'] = $user['coachID'];
                echo json_encode([
                    'success' => true,
                    'coachID' => $user['coachID'],
                    'role' => $user['role'],
                    'coachName' => $user['username'],
                ]);
            } elseif ($user['role'] == 'Analyst') {
                $_SESSION['analystID'] = $user['analystID'];
                echo json_encode([
                    'success' => true,
                    'analystID' => $user['analystID'],
                    'role' => $user['role'],
                    'analystName' => $user['username'],
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Gameplan</title>
    <link rel="icon" type="image/png" href="NU GAMEPLAN.png">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 31, 84, 0.9);
        }
        .container { display: flex; width: 1920px; height: 1080px; background-color: #F6F4F0; border-radius: 8px; overflow: hidden; }
        .left-section { flex: 1; background-color: #D3D3D3; }
        .right-section { flex: 1; display: flex; justify-content: center; align-items: center; background-color: #001F54; }
        .login-box { position: relative; width: 600px; height: 800px; background-color: #F6F4F0; border-radius: 10px; display: flex; flex-direction: column; align-items: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .login-box .welcome { margin-top: 30px; margin-bottom: 16px; color: #001F54; font-size: 24px; font-weight: 600; text-align: center; }
        .login-box .image-container { width: 400px; height: 200px; background: url('NU GAMEPLAN.png') no-repeat center; background-size: contain; }
        .form { width: 100%; display: flex; flex-direction: column; align-items: center; margin-top: 20px; }
        .form .form-group { width: 80%; margin-bottom: 16px; }
        .form label { display: block; font-size: 14px; color: #001F54; font-weight: bold; margin-bottom: 8px; }
        .form input { width: 100%; height: 45px; padding: 2px; border: 1px solid #001F54; border-radius: 10px; font-size: 16px; }
        .form button { width: 150px; height: 45px; background-color: #F5C414; border: none; border-radius: 10px; color: #001F54; font-weight: bold; cursor: pointer; font-size: 18px; margin-top: 20px; }
        .form button:hover { background-color: #e0af10; }
        .slideshow-container { width: 100%; height: 100%; overflow: hidden; display: flex; justify-content: center; align-items: center; }
        .slideshow-container img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; transition: opacity 1s ease-in-out; }
    
    </style>
</head>

<body>
    <div class="container">
        <div class="slideshow-container">
            <img id="slideshow" src="image1.jpg" alt="Slideshow Image">
        </div>
        <div class="right-section">
            <div class="login-box">
                <div class="welcome">WELCOME!</div>
                <div class="image-container"></div>
                <form class="form">
                    <div class="form-group">
                        <label for="login">LOGIN</label>
                        <input type="text" id="login" placeholder="Enter your login">
                    </div>
                    <div class="form-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" placeholder="Enter your password">
                    </div>
                    <button type="submit">LOGIN</button>
                </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Login
    document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.querySelector('form');
    const loginButton = document.querySelector('button');

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const username = document.getElementById('login').value;
        const password = document.getElementById('password').value;

        // Check if the inputs are valid
        if (username === '' || password === '') {
            alert('Username and password are required');
            return;
        }

        // Create the data to send via POST
        const data = new FormData();
        data.append('username', username);
        data.append('password', password);
        data.append('action', 'login'); // Custom action field to specify login action

        // Use fetch API to send the login data
        fetch('http://localhost/GamePlan/Login.php', { // Replace with the correct URL
            method: 'POST',
            body: data
        })
        .then(response => response.json())  // Parse the JSON response
        .then(data => {
            if (data.success) {
                // Successful login - redirect based on role
                if (data.role === 'Coach') {
                    localStorage.setItem('coachID', data.coachID);
                    window.location.href = 'Dashboard_Coach/GD.php'; // Redirect to CommHub.html if the user is a coach
                } else if (data.role === 'Analyst') {
                    localStorage.setItem('analystID', data.analystID);
                    window.location.href = 'Dashboard_AnR/GD.php'; // Redirect to analyst-dashboard.html if the user is an analyst
                } else if (data.role === 'Player') {
                    localStorage.setItem('playerID', data.playerID);
                    window.location.href = 'Player_Profile_Player/Player.php'; // Redirect to Player.php if the user is a player
                }
            } else {
                // If login failed, show the error message
                alert(data.message || 'Invalid username or password');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error processing your request.');
        });
    });
});

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const images = ["image1.jpg", "image2.jpg", "image3.jpg", "image4.jpg", "image5.jpg", "image6.jpg"]; // Add your images
            let index = 0;
            const slideshow = document.getElementById("slideshow");

            function changeImage() {
                index = (index + 1) % images.length; // Loop back to the first image after the last one
                slideshow.style.opacity = 0; // Fade out effect

                setTimeout(() => {
                    slideshow.src = images[index];
                    slideshow.style.opacity = 1; // Fade in effect
                }, 500); // Delay for smooth transition
            }

            setInterval(changeImage, 3000); // Change image every 3 seconds
        });
    </script>

</body>
</html>