<?php
// Start the session if it's not already active

    session_start();


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Connect to the database
$connection = mysqli_connect("localhost", "root", "root", "travle1");

// Error handling for database connection
if (mysqli_connect_error()) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$user_id = $_SESSION['user_id'];
$travel_query = "
    SELECT 
        travle.id AS travel_id, 
        travle.month, 
        travle.year, 
        country.country AS country_name, 
        place.id AS place_id, 
        place.name AS place_name, 
        place.location, 
        place.description, 
        place.photoFileName AS photo, 
        COUNT(DISTINCT `like`.userID) AS likes_count, 
        GROUP_CONCAT(DISTINCT CONCAT(comment.userID, ': ', comment.comment) SEPARATOR '<br>') AS comments,
        COUNT(place.id) AS places_count
    FROM travle
    LEFT JOIN Place AS place ON travle.id = place.travelID
    LEFT JOIN `Like` AS `like` ON place.id = `like`.placeID
    LEFT JOIN Comment AS comment ON place.id = comment.placeID
    LEFT JOIN Country AS country ON travle.countryID = country.id
    WHERE travle.userID = ?
    GROUP BY place.id, travle.id
    ORDER BY travle.id";

$stmt = $connection->prepare($travel_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$travels = [];
while ($row = $result->fetch_assoc()) {
    $travels[] = $row;
}

mysqli_close($connection);
echo json_encode($travels);
?>
