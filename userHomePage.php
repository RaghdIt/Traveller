<?php
session_start();

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id'])) {
    echo "<p> hello</p>";
    header('Location: login.php');
    exit();
}

// Database credentials
$servername = "localhost"; // Adjust if different
$username = "root"; // Replace with your database username
$password = "root"; // Replace with your database password
$dbname = "travle1"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$query = "SELECT firstName, lastName, emailAddress, photoFileName FROM User WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);




$countryQuery = "SELECT id, country FROM Country";
$countryResult = mysqli_query($conn, $countryQuery);

$travelsQuery = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['country'])) {
    $selectedCountry = $_POST['country'];
    $travelsQuery = "SELECT Travle.id, User.firstName, User.lastName, User.photoFileName AS userPhoto, Country.country, Travle.month, Travle.year, 
                     (SELECT COUNT(*) FROM `Like` WHERE `Like`.placeID IN (SELECT id FROM Place WHERE Place.travelID = Travle.id)) AS totalLikes 
                     FROM Travle
                     JOIN User ON Travle.userID = User.id 
                     JOIN Country ON Travle.countryID = Country.id 
                     WHERE Travle.countryID = ?";
    $stmt = mysqli_prepare($conn, $travelsQuery);
    mysqli_stmt_bind_param($stmt, "i", $selectedCountry);
} else {
    $travelsQuery = "SELECT Travle.id, User.firstName, User.lastName, User.photoFileName AS userPhoto, Country.country, Travle.month, Travle.year, 
                     (SELECT COUNT(*) FROM `Like` WHERE `Like`.placeID IN (SELECT id FROM Place WHERE Place.travelID = Travle.id)) AS totalLikes 
                     FROM Travle 
                     JOIN User ON Travle.userID = User.id 
                     JOIN Country ON Travle.countryID = Country.id";
    $stmt = mysqli_prepare($conn, $travelsQuery);
}

mysqli_stmt_execute($stmt);
$travelsResult = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User HomePage</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="User Home Page.css">
</head>
<body>

<header class="header">
    <img src="image/logo.png" alt="Platform Logo">
    <h1>Travel Platform</h1>
</header>

<div class="header">
    <h1 id="welcome-note">Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h1>
    <div class="header-links">
        <a href="Travelpage.php" id="my-travels">My Travels</a>
        <a href="signOut.php" id="sign-out">Sign Out</a>
    </div>
</div>

<main>
    <div class="user-info-section">
        <div class="user-info">
            <h2>Your Information</h2>
            <p id="user-name">Name: <?php echo htmlspecialchars($user['firstName']) . " " . htmlspecialchars($user['lastName']); ?></p>
            <p id="user-email">Email: <?php echo htmlspecialchars($user['emailAddress']); ?></p>
            
        </div>
        

<img id="user-photo" src="<?php echo ($user['photoFileName'] == 'pfp.jpg' ? 'image/pfp.jpg' : 'uploads/' . htmlspecialchars($user['photoFileName'])); ?>" alt="User Photo">



    </div>

    <div class="travels-header">
        <h2>All Travels</h2>
      <div class="filter-section">
    <form method="POST" action="">
        <label for="country">Select Country:</label>
        <select name="country" id="country-filter">
            <option value="" <?php echo (empty($_POST['country'])) ? 'selected' : ''; ?>>All</option>
            <?php while ($country = mysqli_fetch_assoc($countryResult)) : ?>
                <option value="<?php echo htmlspecialchars($country['id']); ?>" <?php echo (isset($_POST['country']) && $_POST['country'] == $country['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($country['country']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
    </form>
</div>
    </div>

    <table id="travels-table">
        <thead>
            <tr>
                <th>Traveler</th>
                <th>Country</th>
                <th>Travel Time</th>
                <th>Total Likes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($travel = mysqli_fetch_assoc($travelsResult)) : ?>
                <tr>
                    <td>
                        <a href="details.php?travel_id=<?php echo htmlspecialchars($travel['id']); ?>" class="traveller-name">
                            <?php echo htmlspecialchars($travel['firstName'] . " " . $travel['lastName']); ?>
                        </a>
                        <a href="details.php?travel_id=<?php echo htmlspecialchars($travel['id']); ?>">
                            
                            <img src="uploads/<?php echo htmlspecialchars($travel['userPhoto']); ?>" alt="User Photo" class="user-photo">
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($travel['country']); ?></td>
                    <td><?php echo htmlspecialchars($travel['month'] . " " . $travel['year']); ?></td>
                    <td><?php echo htmlspecialchars($travel['totalLikes']); ?> ❤️</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if (mysqli_num_rows($travelsResult) == 0) : ?>
        <span id="no-travels-message">There are no travels for this country!</span>
    <?php endif; ?>
</main>

<div class="footer">
    <p>© 2024 Travel Platform | All Rights Reserved</p>
</div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#country-filter').on('change', function() {
        var countryID = $(this).val(); // Get selected country ID
        $.ajax({
            type: 'POST',
            url: 'getTravelsByCountry.php', // PHP file to fetch travels by country
            data: { 'country-filter': countryID },
            dataType: 'json',
            success: function(response) {
                updateTravelsTable(response); // Update the table with the response data
            },
            error: function() {
                console.log("Error fetching travel data.");
            }
        });
    });
});

function updateTravelsTable(travels) {
    var tableBody = $('#travels-table tbody');
    tableBody.empty(); // Clear any previous rows
    $.each(travels, function(index, travel) {
        var row = `<tr>
            <td>
                <a href="details.php?travel_id=${travel.travelID}" class="traveller-name">
                    ${travel.firstName} ${travel.lastName}
                </a>
                <a href="details.php?travel_id=${travel.travelID}">
                    <img src="uploads/${travel.userPhoto}" alt="User Photo" class="user-photo">
                </a>
            </td>
            <td>${travel.country}</td>
            <td>${travel.month} ${travel.year}</td>
            <td>${travel.totalLikes} ❤️</td>
        </tr>`;
        tableBody.append(row);
    });
}

</script>


</body>
</html>

<?php mysqli_close($conn); ?>
