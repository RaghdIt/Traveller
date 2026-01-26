<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$conn = new mysqli('localhost', 'root', 'root', 'travle1');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : 0;
$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($place_id > 0) {
    if ($action === 'like') {
        // Check if the user has already liked the place
        $check_like_query = "SELECT * FROM `like` WHERE userID = ? AND placeID = ?";
        $stmt = $conn->prepare($check_like_query);
        $stmt->bind_param("ii", $user_id, $place_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $like_query = "INSERT INTO `like` (userID, placeID) VALUES (?, ?)";
            $stmt = $conn->prepare($like_query);
            $stmt->bind_param("ii", $user_id, $place_id);
            $stmt->execute();
        }
    } elseif ($action === 'unlike') {
        $unlike_query = "DELETE FROM `like` WHERE userID = ? AND placeID = ?";
        $stmt = $conn->prepare($unlike_query);
        $stmt->bind_param("ii", $user_id, $place_id);
        $stmt->execute();
    }

    $count_query = "SELECT COUNT(*) as like_count FROM `like` WHERE placeID = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("i", $place_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode(['success' => true, 'like_count' => $data['like_count']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid place ID']);
}
?>
