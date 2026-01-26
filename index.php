<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Site</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            background-image: url(https://estaticos-cdn.prensaiberica.es/clip/d235ca22-2315-4302-9a5f-2f5a45635eb4_source-aspect-ratio_default_0.jpg);
            background-size: cover;
            background-position: center;
            height: calc(100% - 100px); 
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            animation: gradientShift 10s ease infinite;
            color: white;
        }

        .header img {
            height: 80px;
        }

        .header h1 {
            margin: 0;
        }

        .footer {
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
            width: 100%;
            bottom: 0;
        }

        .content {
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px; 
        }

        h1 {
            margin-bottom: 50px; 
            font-size: 3em; 
            color: white; 
            text-align: center; 
        }

        .buttons {
            display: flex;
            gap: 50px;
            justify-content: center;
            margin-top: 40px; 
        }

        .btn {
            text-decoration: none;
            padding: 10px 20px; 
            border-radius: 3px;
            font-size: 1em;
        }

        .login {
            background-color: #38598b;
            color: white;
        }

        .login:hover {
            background-color: #0056b3;
        }

        .signup {
            background-color: #a2a8d3;
            color: white;
        }

        .signup:hover {
            background-color: lightblue;
        }
    </style>
</head>
<body>
    <header class="header">
        <img src="image/logo.png" alt="Platform Logo"> <!-- Replace 'logo.png' with your actual logo file -->
        <h1>Travel Platform</h1>
    </header>

    <div class="container">
        <h1>Explore the world</h1> 
        <div class="content">
            <div class="buttons">
                <a href="login.php" class="btn login">Log In</a>
                <a href="signup.php" class="btn signup">Sign Up</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© 2024 Travel Platform | All Rights Reserved</p>
    </footer>
</body>
</html>


<?php

?>