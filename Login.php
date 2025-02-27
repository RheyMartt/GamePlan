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
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: rgba(0, 31, 84, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            background-color: #F6F4F0;
            overflow: hidden;
        }

        .slideshow-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #001F54;
        }

        .slideshow-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 1s ease-in-out;
        }

        .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #001F54;
        }

        .login-box {
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background-color: #F6F4F0;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-box .welcome {
            margin-top: 20px;
            margin-bottom: 16px;
            color: #001F54;
            font-size: 24px;
            font-weight: 600;
        }

        .login-box .image-container {
            width: 100%;
            height: 150px;
            background: url('NU GAMEPLAN.png') no-repeat center;
            background-size: contain;
            margin-bottom: 20px;
        }

        .form{
            width: 100%;
        }

        .form .form-group {
            margin-bottom: 16px;
        }

        .form label {
            display: block;
            font-size: 14px;
            color: #001F54;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: left;
        }

        .form input {
            width: 100%;
            height: 45px;
            padding: 10px;
            border: 1px solid #001F54;
            border-radius: 10px;
            font-size: 16px;
        }

        .form button {
            width: 100%;
            height: 45px;
            background-color: #F5C414;
            border: none;
            border-radius: 10px;
            color: #001F54;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
        }

        .form button:hover {
            background-color: #e0af10;
        }

        .signup-section {
            margin-top: 20px;
            color: #001F54;
            font-size: 16px;
        }

        .signup-section button {
            background: none;
            border: none;
            color: #F5C414;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            width: 90%;
            max-width: 400px;
            background-color: #F6F4F0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #001F54;
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            color: #001F54;
            margin-bottom: 16px;
        }

        .form {
            width: 100%;
        }

        .form .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form label {
            display: block;
            font-size: 14px;
            color: #001F54;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form input, .form select {
            width: 100%;
            height: 45px;
            padding: 10px;
            border: 1px solid #001F54;
            border-radius: 10px;
            font-size: 16px;
        }

        .form button {
            width: 100%;
            height: 45px;
            background-color: #F5C414;
            border: none;
            border-radius: 10px;
            color: #001F54;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
        }

        .form button:hover {
            background-color: #e0af10;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .slideshow-container {
                display: none; /* Hide slideshow on smaller screens */
            }

            .right-section {
                width: 100%;
                height: 100vh;
                padding: 20px;
            }

            .login-box {
                width: 100%;
                max-width: 100%;
                height: auto;
                padding: 20px;
            }

            .login-box .image-container {
                height: 100px;
            }
        }

        @media (max-width: 480px) {
            .login-box .welcome {
                font-size: 20px;
            }

            .login-box .image-container {
                height: 80px;
            }

            .form input {
                height: 40px;
                font-size: 14px;
            }

            .form button {
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Slideshow Section -->
        <div class="slideshow-container">
            <img id="slideshow" src="image1.jpg" alt="Slideshow Image">
        </div>

        <!-- Login Section -->
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

            <div class="signup-section">
                <label for="signup">Don't have an account?</label>
                <button class="signup-section button" onclick="openModal()">SIGN UP</button>
            </div>
        </div>
    </div>

    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="modal-header">Sign Up</h2>
            <form class="form">
                <div class="form-group">
                    <label for="role">Select Role</label>
                    <select id="role" required>
                        <option value="" disabled selected>Choose role</option>
                        <option value="Player">Player</option>
                        <option value="Coach">Coach</option>
                        <option value="Analyst">Analyst</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" required>
                </div>
                <div class="form-group">
                    <label for="mainRole">Main Role</label>
                    <input type="text" id="mainRole" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        // Login Script (unchanged)
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.querySelector('form');
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const username = document.getElementById('login').value;
                const password = document.getElementById('password').value;

                if (username === '' || password === '') {
                    alert('Username and password are required');
                    return;
                }

                const data = new FormData();
                data.append('username', username);
                data.append('password', password);
                data.append('action', 'login');

                fetch('http://localhost/GamePlan/Login.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.role === 'Coach') {
                            window.location.href = 'Dashboard_Coach/GD.php';
                        } else if (data.role === 'Analyst') {
                            window.location.href = 'Feedback_n_Analyzation_AnR/FnA.php';
                        } else if (data.role === 'Player') {
                            window.location.href = 'Player_Profile_Player/Player.php';
                        }
                    } else {
                        alert(data.message || 'Invalid username or password');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error processing your request.');
                });
            });
        });

        // Slideshow Script
        document.addEventListener('DOMContentLoaded', function () {
            const images = ["image1.jpg", "image2.jpg", "image3.jpg", "image4.jpg", "image5.jpg", "image6.jpg"];
            let index = 0;
            const slideshow = document.getElementById("slideshow");

            function changeImage() {
                index = (index + 1) % images.length;
                slideshow.style.opacity = 0;
                setTimeout(() => {
                    slideshow.src = images[index];
                    slideshow.style.opacity = 1;
                }, 500);
            }

            setInterval(changeImage, 3000);
        });

        function openModal() {
            document.getElementById("signupModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("signupModal").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("signupModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>