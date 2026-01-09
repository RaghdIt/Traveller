<?php
session_start();

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'travle1'); // Use correct database name

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $email = htmlspecialchars(trim($_POST['emailAddress']));
    $password = password_hash(htmlspecialchars(trim($_POST['password'])), PASSWORD_DEFAULT);

    // Check if email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM User WHERE emailAddress = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "The email address is already taken. Please choose another one.";
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // Get the file details
            $photoTmpName = $_FILES['photo']['tmp_name'];
            $photoOriginalName = basename($_FILES['photo']['name']); // Keep the original file name
            $photoExtension = strtolower(pathinfo($photoOriginalName, PATHINFO_EXTENSION));

            // Validate file type (ensure it's an image)
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($photoExtension, $validExtensions)) {
                $photoUploadPath = 'uploads/' . $photoOriginalName; // Use the original file name and path

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($photoTmpName, $photoUploadPath)) {
                    // File upload successful, store the original photo file name in the database
                } else {
                    $error = "There was an error uploading the photo.";
                }
            } else {
                $error = "Invalid photo file type. Please upload a JPG, JPEG, PNG, or GIF file.";
            }
        } else {
            // If no file is uploaded, set a default value (you could also skip this if you want a photo mandatory)
            $photoOriginalName = 'pfp.jpg'; // Default photo
        }

        if (!empty($email) && !empty($password) && !empty($firstName) && !empty($lastName)) {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO User (firstName, lastName, emailAddress, password, photoFileName) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $password, $photoOriginalName);

            if ($stmt->execute()) {
                // Get the ID of the newly created user
                $newUserId = $stmt->insert_id;

                // Assuming signup process and user insertion is successful, and $newUserId is the ID of the newly created user
                $_SESSION['user_id'] = $newUserId; // Set the session with the new user's ID

                // Redirect to userHomePage
                header('Location: userHomePage.php');
                exit();
            } else {
                $error = "Error during signup. Please try again.";
            }

            $stmt->close();
        } else {
            $error = "Please fill in all required fields.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            background-size: 600% 600%;
            animation: gradientShift 10s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: space-between; 
            align-items: center;
            margin: 0;
            min-height: 100vh;
        }
        
        .signup-container {
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
        
        .signup-container:hover {
            transform: translateY(-10px);
        }

        .signup-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #3a5b7d;
            font-weight: 600;
        }

        #profilePicContainer {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            cursor: pointer;
            width: 120px;
            height: 120px;
        }

        #profilePicPreview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4e8d8f;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background-image: url('image/pfp.jpg'); /* Default profile picture */
            background-size: cover;
            background-position: center;
            transition: transform 0.3s ease;
        }

        #profilePicPreview:hover {
            transform: scale(1.1);
        }

        .signup-container input[type="text"], 
        .signup-container input[type="email"], 
        .signup-container input[type="password"] {
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

        .signup-container input[type="text"]:focus,
        .signup-container input[type="email"]:focus,
        .signup-container input[type="password"]:focus {
            border-color: #4e8d8f;
        }

        .signup-container input[type="file"] {
            display: none; /* Hide the file input */
        }

        .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 50px;
            border: 2px solid #b5d2d4;
            background-color: #fff;
            color: #3a5b7d;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .upload-btn:hover {
            background-color: #4e8d8f;
            color: #fff;
        }

        .signup-container input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #4e8d8f, #6b9ac4);
            border: none;
            border-radius: 50px;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .signup-container input[type="submit"]:hover {
            background: linear-gradient(to right, #6b9ac4, #4e8d8f);
            transform: scale(1.05);
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: -10px;
            display: none;
        }
        a {
            text-decoration: none;
            color:#9c7cb0
        }
        p {
            color:#3a5b7d
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        #hiddenFileInput {
            display: none;
        }

        .main-content {
         flex: 1; 
         display: flex;
         justify-content: center;
         align-items: center;
         }

        .header {
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            color: white;
            width: 100%;
            box-sizing: border-box;
        }

        .header img {
            height: 80px;
            display: block; 
            background: transparent; 
            margin-right: 20px;
        }
        .header h2 {
            color: white;
            margin: 0; 
        }

        .footer {
            text-align: center;
            width: 100%;
            padding: 20px;
            color: rgb(255, 255, 255); 
            box-sizing: border-box;
            position: relative; 
            bottom: 0;
        }
        
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            cursor: pointer;
            width: 30px;
            height: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="image/logo.png" alt="Logo">
        <h2></h2>
    </div>

    <div class="signup-container">
        <a href="index.php">
            <img src="image/back.png" alt="back" class="back-button">
        </a>

        <h2>Create Your Account</h2>

<form action="signup.php" method="POST" enctype="multipart/form-data">
            <div id="profilePicContainer">
                <img id="profilePicPreview" src="image/pfp.jpg" alt="Profile Picture">
                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewProfilePic(event)">
            </div>
            <input type="text" name="firstName" placeholder="First Name" required>
            <input type="text" name="lastName" placeholder="Last Name" required>
            <input type="email" name="emailAddress" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <label for="photo" class="upload-btn">Upload A Profile Picture</label>

            <input type="submit" value="Sign Up">
            <p>Already have an account? <a href="login.php">Log in here</a></p>
        </form>

        <?php if (!empty($error)): ?>
            <div style="color: red; font-weight: bold; margin-bottom: 10px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

      
    </div>
      <script>
        function previewProfilePic(event) {
            const preview = document.getElementById('profilePicPreview');
            preview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</body>
</html>
