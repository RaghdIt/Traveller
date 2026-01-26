<?php

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'travle1');

// Check for connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get country ID from the request
$countryID = isset($_POST['country-filter']) ? $_POST['country-filter'] : 0;

if ($countryID) {
    // Query for travels related to the selected country
    $sql = "
        SELECT 
            Travle.id AS travelID, 
            User.firstName, 
            User.lastName, 
            User.photoFileName AS userPhoto, 
            Country.country, 
            Travle.month, 
            Travle.year, 
            (SELECT COUNT(*) FROM `like` WHERE `like`.placeID IN (SELECT id FROM Place WHERE Place.travelID = Travle.id)) AS totalLikes
        FROM Travle
        JOIN User ON Travle.userID = User.id
        JOIN Country ON Travle.countryID = Country.id
        WHERE Travle.countryID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $countryID); // Bind the countryID parameter
    $stmt->execute();
    $result = $stmt->get_result();
    
    $travels = array();
    while ($row = $result->fetch_assoc()) {
        $travels[] = $row;
    }
    
    // Return the result as JSON
    echo json_encode($travels);
} else {
    echo json_encode([]);
}

$conn->close();
?>
