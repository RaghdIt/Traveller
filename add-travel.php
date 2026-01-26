<?php
// Start session for user management
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Database connection details
$servername = "localhost"; // Replace with your server name
$username = "root";        // Replace with your database username
$password = "root";        // Replace with your database password
$dbname = "travle1";       // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Backend logic to handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_SESSION['user_id']; // Assuming user ID is stored in session
    $month = $_POST['month'];
    $year = $_POST['year'];
    $countryID = $_POST['countryID'];

    // Insert new travel entry
    $query = "INSERT INTO travle (userID, month, year, countryID) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isii", $userID, $month, $year, $countryID);
    mysqli_stmt_execute($stmt);

    // Get the last inserted travel ID and redirect to add places
    $travelID = mysqli_insert_id($conn);
    header("Location: add-place.php?travelID=" . $travelID);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Travel</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #9cbed4, #a3b4d7);
            color: #333;
        }

        /* Header styling */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
        }

        .header img {
            height: 60px;
        }

        .header nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .header nav a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Form Container Styling */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #4a4a4a;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
            font-weight: bold;
            color: #555;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        button {
            background-color: #5a8f92;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4a7a7a;
        }

        /* Footer Styling */
        .footer {
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Header with Logo and Navigation -->
<div class="header">
    <img src="image\logo.png" alt="Logo">
    <nav>
        <a href="userHomePage.php">User's Homepage</a>
        <a href="Travelpage.php">User's Travel Page</a>
    </nav>
</div>

<div class="container">
    <div class="form-container">
        <h2>New Travel</h2>
        <form method="POST">
            <label for="month">Travel Month:</label>
            <select name="month" id="month" required>
                <option value="">Select month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

            <label for="year">Year:</label>
            <select name="year" id="year" required>
                <option value="">Select year</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>

            <label for="country">Country:</label>
            <select name="countryID" id="country" required>
                <option value="">Select country</option>
                <?php
                // Fetch countries from the database
                $query = "SELECT id, country FROM Country";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['id'] . "'>" . $row['country'] . "</option>";
                }
                ?>
            </select>

            <button type="submit">Next</button>
        </form>
    </div>
</div>

<div class="footer">
    © 2024 Travel Platform | All Rights Reserved
</div>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>