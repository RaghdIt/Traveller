<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'travle1'); // Use correct database name

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['emailAddress']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password FROM user WHERE LOWER(emailAddress) = LOWER(?)");
        
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $hashedPassword = $user['password'];

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: userHomePage.php");
                exit();
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "Email not found";
        }
    } else {
        $error = "Please fill all fields";
    }
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            background-size: 600% 600%;
            animation: gradientShift 10s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .main-content { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; width: 100%; position: relative; }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease-in-out;
            position: relative;
        }
        .login-container:hover { transform: translateY(-10px); }
        .login-container h2, h1 { margin-bottom: 20px; font-size: 28px; color: #3a5b7d; font-weight: 600; }
        .login-container input[type="text"], .login-container input[type="password"] {
            width: 95%;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #b5d2d4;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .login-container input[type="text"]:focus, .login-container input[type="password"]:focus { border-color: #4e8d8f; }
        .login-container input[type="submit"] {
            width: 100%; padding: 15px; background: linear-gradient(to right, #4e8d8f, #6b9ac4);
            border: none; border-radius: 50px; color: #fff; font-weight: bold; font-size: 18px; cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); transition: all 0.3s ease;
        }
        .login-container input[type="submit"]:hover { background: linear-gradient(to right, #6b9ac4, #4e8d8f); transform: scale(1.05); }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .header { display: flex; align-items: center; justify-content: space-between; padding: 20px; color: white; width: 100%; box-sizing: border-box; }
        .header img { height: 80px; display: block; background: transparent; margin-right: 20px; }
        .header h2 { color: white; margin: 0; }
        .footer { text-align: center; padding: 20px; color: white; width: 100%; box-sizing: border-box; }
        a { text-decoration: none; color:#8937bd; }
        p { color:#3a5b7d; }
        .back-button { position: absolute; top: 10px; left: 10px; cursor: pointer; width: 30px; height: 30px; }
    </style>
</head>

<body>
    <div class="main-content">
     <div class="header">
            <img src="image/logo.png" alt="Logo">
            <h2></h2>
        </div>   

        <div class="login-container">
            <a href="index.php">
                <img src="image/back.png" alt="Return" class="back-button">
            </a>

            <h1>Welcome Back!</h1>
            <h2>Log In</h2>
            <!-- Display error message -->
<?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
        <form action="login.php" method="post"> 
            <input type="text" name="emailAddress" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Log In">
            <p> Don't have an account?  <a href="signup.php">Sign Up Here </a> </p>
        </form>
    </div>
    <footer class="footer">
        <p>© 2024 Travel Platform | All Rights Reserved</p>
    </footer>
</body>
</html>





