<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$connection = mysqli_connect(
    "localhost",
    "root",
    "root",
    "travle1"
);


if (mysqli_connect_error()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle AJAX delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $data = json_decode(file_get_contents('php://input'), true);
    $travel_id = $data['travel_id'] ?? null;
    
    if (!$travel_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid travel ID.']);
        exit();
    }

    $connection->begin_transaction();

    try {
        $delete_like_query = "DELETE FROM `like` WHERE placeID IN (SELECT id FROM place WHERE travelID = ?)";
        $stmt = $connection->prepare($delete_like_query);
        $stmt->bind_param("i", $travel_id);
        $stmt->execute();

        $delete_comment_query = "DELETE FROM comment WHERE placeID IN (SELECT id FROM place WHERE travelID = ?)";
        $stmt = $connection->prepare($delete_comment_query);
        $stmt->bind_param("i", $travel_id);
        $stmt->execute();

        $delete_place_query = "DELETE FROM place WHERE travelID = ?";
        $stmt = $connection->prepare($delete_place_query);
        $stmt->bind_param("i", $travel_id);
        $stmt->execute();

        $delete_travel_query = "DELETE FROM travle WHERE id = ?";
        $stmt = $connection->prepare($delete_travel_query);
        $stmt->bind_param("i", $travel_id);
        $stmt->execute();

        $connection->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $connection->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to delete the travel.']);
    }
    exit();
}

// Fetch user information for display
$user_id = $_SESSION['user_id'];
$user_query = "SELECT firstName FROM user WHERE id = ?";
$stmt = $connection->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

$user_name = $user ? $user['firstName'] : "Guest"; // Fallback if user data is not found
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user_name); ?>'s Travel Page</title>
    <style>
        /* General body style */
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #b9ceeb;
            display: flex;
            flex-direction: column;
        }
        
        h1 {
            text-align: center;
            color: white;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            padding: 20px;
            margin: 0;
        }

        .navigation-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 80px;
        }

        .navigation a, .add-new-travel a {
            color: white;
            background-color: #6b9ac4;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .navigation a:hover, .add-new-travel a:hover {
            background-color: #5a8baf;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #6b9ac4;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
    <script>
        function loadTravels() {
            fetch('fetch_travels.php')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector("tbody");
                    tableBody.innerHTML = ""; // Clear previous data
                    let travelCount = 1;  // Start numbering from 1

                    let travelEntries = {};

                    // Process data and organize it by travel ID
                    data.forEach(row => {
                        const travelId = row.travel_id ?? '';
                        const month = row.month ?? '';
                        const year = row.year ?? '';
                        const countryName = row.country_name ?? 'N/A';
                        const placeName = row.place_name ?? 'N/A';
                        const location = row.location ?? 'N/A';
                        const description = row.description ?? 'N/A';
                        const photo = row.photo 
    ? `<img src="uploads/${row.photo}" alt="${placeName}">`
    : 'No Image';

                        const likesCount = row.likes_count ?? 0;
                        const comments = row.comments ?? 'No Comments';

                        if (!travelEntries[travelId]) {
                            travelEntries[travelId] = {
                                month,
                                year,
                                countryName,
                                places: [],
                                travelId
                            };
                        }

                        travelEntries[travelId].places.push({
                            placeName,
                            location,
                            description,
                            photo,
                            likesCount,
                            comments
                        });
                    });

                    for (const travelId in travelEntries) {
                        const travel = travelEntries[travelId];
                        const placesCount = travel.places.length;

                        tableBody.innerHTML += `
                            <tr id="travel-row-${travelId}">
                                <td rowspan="${placesCount}">
                                    <strong>${travelCount}</strong><br>
                                    <a href="editTravelPage.php?travel_id=${travelId}" class="edit-link">Edit Travel</a><br>
                                    <a href="javascript:void(0);" class="delete-link" onclick="deleteTravel(${travelId})">Delete Travel</a>
                                </td>
                                <td rowspan="${placesCount}">${travel.month} ${travel.year}</td>
                                <td rowspan="${placesCount}">${travel.countryName}</td>
                                <td>${travel.places[0].placeName}</td>
                                <td>${travel.places[0].location}</td>
                                <td>${travel.places[0].description}</td>
                                <td>${travel.places[0].photo}</td>
                                <td>${travel.places[0].likesCount}</td>
                                <td>${travel.places[0].comments}</td>
                            </tr>`;

                        for (let i = 1; i < placesCount; i++) {
                            const place = travel.places[i];
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${place.placeName}</td>
                                    <td>${place.location}</td>
                                    <td>${place.description}</td>
                                    <td>${place.photo}</td>
                                    <td>${place.likesCount}</td>
                                    <td>${place.comments}</td>
                                </tr>`;
                        }

                        travelCount++;  // Increment the travel number
                    }
                })
                .catch(error => console.error('Error loading travels:', error));
        }

        function deleteTravel(travelId) {
            if (!confirm('Are you sure you want to delete this travel?')) return;

            fetch('Travelpage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ travel_id: travelId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`travel-row-${travelId}`).remove();
                } else {
                    alert('Failed to delete the travel.');
                }
            })
            .catch(error => console.error('Error deleting travel:', error));
        }

        document.addEventListener("DOMContentLoaded", loadTravels);
    </script>
</head>
<body>

<h1><?php echo htmlspecialchars($user_name); ?>'s Travels</h1>

<div class="navigation-container">
    <a href="add-travel.php">Add New Travel</a>
    <div class="navigation">
        <a href="userHomePage.php">Back to Homepage</a>
        <a href="login.php">Log-out</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2">Travel</th>
            <th rowspan="2">Travel Time</th>
            <th rowspan="2">Country</th>
            <th colspan="6">Places</th>
        </tr>
        <tr>
            <th>Place Name</th>
            <th>Location</th>
            <th>Description</th>
            <th>Photo</th>
            <th>Likes</th>
            <th>Comments</th>
        </tr>
    </thead>
    <tbody>
       
    </tbody>
</table>

</body>
</html>
