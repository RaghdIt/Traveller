<?php
session_start();

// Connect to the database
$connection = mysqli_connect("localhost", "root", "root", "travle1");

// Error handling for database connection
if (mysqli_connect_error()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get travel_id from the URL
$travel_id = isset($_GET['travel_id']) ? (int)$_GET['travel_id'] : 0;

// Query to get travel information by ID
$travel_query = "SELECT * FROM travle WHERE id = ?";
$stmt = $connection->prepare($travel_query);
$stmt->bind_param("i", $travel_id);
$stmt->execute();
$travel_result = $stmt->get_result();
$travel = $travel_result->fetch_assoc();

if (!$travel) {
    die("Travel not found with ID: " . $travel_id);
}

// Query to get all countries from the country table
$country_query = "SELECT id, country FROM country";
$country_result = mysqli_query($connection, $country_query);

if (!$country_result) {
    die("Failed to retrieve countries: " . mysqli_error($connection));
}

// Store countries in an array for dropdown
$countries = [];
while ($row = mysqli_fetch_assoc($country_result)) {
    $countries[] = $row;
}

// Query to get places associated with the travel
$places_query = "SELECT id, name, location, description, photoFileName FROM place WHERE travelID = ?";
$stmt = $connection->prepare($places_query);
$stmt->bind_param("i", $travel_id);
$stmt->execute();
$places_result = $stmt->get_result();
$places = $places_result->fetch_all(MYSQLI_ASSOC);

// Handle travel details update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_travel'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $country_id = $_POST['country_id'];

    // Update the travel entry in the database
    $update_query = "UPDATE travle SET month = ?, year = ?, countryID = ? WHERE id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("sisi", $month, $year, $country_id, $travel_id);
    $stmt->execute();

    // Redirect to userHomePage.php regardless of whether an update happened
    header("Location: userHomePage.php");
    exit();
}

// Handle place update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_place'])) {
    $place_id = $_POST['place_id'];
    $place_name = $_POST['place_name'];
    $place_location = $_POST['place_location'];
    $place_description = $_POST['place_description'];

    $new_place_photo = $_FILES['place_photo']['name'];
    $place_photo_file = $new_place_photo ? $new_place_photo : $_POST['old_place_photo'];

    if ($new_place_photo) {
        move_uploaded_file($_FILES['place_photo']['tmp_name'], 'uploads/' . $new_place_photo);
    }

    $update_place_query = "UPDATE place SET name = ?, location = ?, description = ?, photoFileName = ? WHERE id = ?";
    $stmt = $connection->prepare($update_place_query);
    $stmt->bind_param("ssssi", $place_name, $place_location, $place_description, $place_photo_file, $place_id);
    $stmt->execute();

    // Redirect to userHomePage.php regardless of whether an update happened
    header("Location: userHomePage.php");
    exit();
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Travel</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #b9ceeb;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
        }

        .header h1 {
            margin: 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 20px;
        }

        .form-section, .places-section {
            width: 100%;
            padding: 20px;
            border: 1px solid #007BFF;
            border-radius: 5px;
            background-color: white;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        select, input[type="text"], textarea, input[type="file"] {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #4e8d8f;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        button:hover {
            background-color: #003366;
        }

        .place-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .place-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .footer {
            background: linear-gradient(270deg, #4e8d8f, #6b9ac4, #9c7cb0);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
            width: 100%;
            bottom: 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Edit Travel Entry</h1>
    </header>

    <div class="container">
        <div class="form-section">
            <form action="editTravelPage.php?travel_id=<?php echo $travel_id; ?>" method="POST">
                <h2>Travel Details</h2>

                <label for="month">Travel Month:</label>
                <select name="month" required>
                    <?php
                    $months = [
                        "January", "February", "March", "April", "May", "June", 
                        "July", "August", "September", "October", "November", "December"
                    ];
                    foreach ($months as $m) {
                        $selected = $travel['month'] == $m ? 'selected' : '';
                        echo "<option value='$m' $selected>$m</option>";
                    }
                    ?>
                </select>

                <label for="year">Year:</label>
                <select name="year" required>
                    <option value="2024" <?php if ($travel['year'] == '2024') echo 'selected'; ?>>2024</option>
                    <option value="2025" <?php if ($travel['year'] == '2025') echo 'selected'; ?>>2025</option>
                    <option value="2026" <?php if ($travel['year'] == '2026') echo 'selected'; ?>>2026</option>
                </select>

                <label for="country_id">Country:</label>
                <select name="country_id" required>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?php echo $country['id']; ?>" <?php if ($travel['countryID'] == $country['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($country['country']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="update_travel">Update Travel</button>
            </form>
        </div>

        <?php if (!empty($places)): ?>
            <div class="places-section">
                <h2>Places (Total: <?php echo count($places); ?>)</h2>
                <?php foreach ($places as $place): ?>
                    <div class="place-item">
                        <h3><?php echo htmlspecialchars($place['name']); ?> (<?php echo htmlspecialchars($place['location']); ?>)</h3>
                        <form action="editTravelPage.php?travel_id=<?php echo $travel_id; ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="place_id" value="<?php echo $place['id']; ?>">
                            <input type="hidden" name="old_place_photo" value="<?php echo $place['photoFileName']; ?>">

                            <label for="place_name_<?php echo $place['id']; ?>">Place Name:</label>
                            <input type="text" name="place_name" value="<?php echo htmlspecialchars($place['name']); ?>" required>

                            <label for="place_location_<?php echo $place['id']; ?>">Location:</label>
                            <input type="text" name="place_location" value="<?php echo htmlspecialchars($place['location']); ?>" required>

                            <label for="place_description_<?php echo $place['id']; ?>">Description:</label>
                            <textarea name="place_description" rows="3"><?php echo htmlspecialchars($place['description']); ?></textarea>

                            <label for="place_photo_<?php echo $place['id']; ?>">Upload New Photo:</label>
                            <input type="file" name="place_photo">

                            <?php if ($place['photoFileName']): ?>
                                <img src="<?php echo htmlspecialchars($place['photoFileName']); ?>" alt="Place Photo">
                            <?php endif; ?>

                            <button type="submit" name="update_place">Update Place</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        &copy; 2024 Travel Planner
    </footer>

</body>
</html>
