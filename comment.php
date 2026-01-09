<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'travle1'); // Update with your credentials
if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

// Get and validate input
$user_id = $_SESSION['user_id'];
$place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : 0;
$comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

if ($place_id <= 0 || empty($comment_text)) {
    echo json_encode(['success' => false]);
    exit();
}

// Insert comment into the database
$query = "INSERT INTO comment (placeID, userID, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $place_id, $user_id, $comment_text);

if ($stmt->execute()) {
    echo json_encode(['success' => true]); // Return success
} else {
    echo json_encode(['success' => false]); // Return failure
}

$stmt->close();
$conn->close();
?>
