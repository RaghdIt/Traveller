<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "travle1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $travelID = $_POST['travelID'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $action = $_POST['action'];

    // Ensure uploads directory exists
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file upload if a photo is provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photoFileName = $uploadDir . $travelID . '_' . basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoFileName)) {
            die("Error: Failed to upload the file.");
        }
    } else {
        $photoFileName = ''; // Set as an empty string if no file is uploaded
    }

    // Insert the place into the database
    $query = "INSERT INTO Place (travelID, name, location, description, photoFileName) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $travelID, $name, $location, $description, $photoFileName);
    
    if ($stmt->execute()) {
        if ($action == "add_more") {
            header("Location: add-place.php?travelID=" . $travelID);
        } else {
            header("Location: userHomePage.php");
        }
    } else {
        die("Error: Could not add the place. " . $stmt->error);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Visited Places in Your Travel</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #9cbed4, #a3b4d7);
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .header nav a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h2 {
            color: #4a4a4a;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            margin-top: 15px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"], input[type="file"], textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            background-color: #4e8d8f;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            margin-top: 15px;
            padding: 12px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #38598b;
        }

        .footer {
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Travel Platform</h1>
    <nav>
        <a href="user_homepage.php">User's Homepage</a>
        <a href="user_travel_page.php">User's Travel Page</a>
    </nav>
</div>

<div class="container">
    <div class="form-container">
        <h2>Add Visited Places in Your Travel</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="travelID" value="<?php echo htmlspecialchars($_GET['travelID'] ?? ''); ?>">

            <label for="name">Place Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter place name" required>

            <label for="location">Location/City:</label>
            <input type="text" id="location" name="location" placeholder="Enter location or city" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter description" required></textarea>

            <label for="photo">Upload Photo:</label>
            <input type="file" id="photo" name="photo">

            <button type="submit" name="action" value="add_more">Add Another Place</button>
            <button type="submit" name="action" value="done">Done</button>
        </form>
    </div>
</div>

<div class="footer">
    © 2024 Travel Platform | All Rights Reserved
</div>

</body>
</html>

<?php
$conn->close();
?>