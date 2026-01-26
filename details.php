<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'travle1'); // Update with your credentials

// Check for connection errors
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo "<p>Could not connect to the database. Please try again later.</p>";
    exit();
}

// Get travel ID from query string
$travel_id = isset($_GET['travel_id']) ? intval($_GET['travel_id']) : 0;

if ($travel_id > 0) {
    // Fetch travel details along with country name and traveler photo
    $query = "
        SELECT t.id, t.month, t.year, c.country, u.firstName, u.lastName, u.photoFileName 
        FROM travle t
        JOIN country c ON t.countryID = c.id
        JOIN user u ON t.userID = u.id
        WHERE t.id = ?;
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $travel_id);
    $stmt->execute();
    $travel_result = $stmt->get_result();
    $travel = $travel_result->fetch_assoc();
    
    if (!$travel) {
        echo "<p>Travel details not found.</p>";
        exit();
    }
    
    
    // Fetch places associated with the travel
    $places_query = "SELECT * FROM place WHERE travelID = ?;";
    $stmt = $conn->prepare($places_query);
    $stmt->bind_param("i", $travel_id);
    $stmt->execute();
    $places_result = $stmt->get_result();
} else {
    echo "<p>Invalid travel ID.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Details</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #b9ceeb;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .place {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fafafa;
        }
        .add-comment textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .add-comment button, .like-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #38598b;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }
        .like-button {
            background-color: #a2a8d3;
        }
        .like-button:hover {
            background-color: #218838;
        }
        .traveler-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.like-button').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var place_id = button.data('place-id');
                var action = button.hasClass('liked') ? 'unlike' : 'like';
                
                $.ajax({
                    type: 'POST',
                    url: 'like.php',
                    data: { place_id: place_id, action: action },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            button.text('Like (' + response.like_count + ')');
                            button.toggleClass('liked');
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <!-- Traveler Info -->
        <h1>Traveler: <?php echo htmlspecialchars($travel['firstName']) . ' ' . htmlspecialchars($travel['lastName']); ?></h1>
        <img id="user-photo" src="<?php echo ($travel['photoFileName'] == 'pfp.jpg' ? 'image/pfp.jpg' : 'uploads/' . htmlspecialchars($travel['photoFileName'])); ?>" alt="User Photo" style="width: 50px;
    height: 50px;
    vertical-align: middle;
    border-radius: 50%;">
        <p>Traveling to: <?php echo htmlspecialchars($travel['country']); ?></p>
        <p>In: <?php echo htmlspecialchars($travel['month']) . ' ' . htmlspecialchars($travel['year']); ?></p>

        <?php while($place = $places_result->fetch_assoc()) {
            // Fetch the like count
            $like_query = "SELECT COUNT(*) as like_count FROM `like` WHERE placeID = ?;";
            $stmt = $conn->prepare($like_query);
            $stmt->bind_param("i", $place['id']);
            $stmt->execute();
            $like_result = $stmt->get_result();
            $like_data = $like_result->fetch_assoc();
            $like_count = $like_data['like_count'];

            // Check if the user has already liked the place
            $user_id = $_SESSION['user_id'];
            $user_like_query = "SELECT * FROM `like` WHERE userID = ? AND placeID = ?";
            $stmt = $conn->prepare($user_like_query);
            $stmt->bind_param("ii", $user_id, $place['id']);
            $stmt->execute();
            $user_like_result = $stmt->get_result();
            $user_liked = $user_like_result->num_rows > 0;
            
        ?>

        <div class="place">
            <h3><?php echo htmlspecialchars($place['name']); ?></h3>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($place['location']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($place['description']); ?></p>
            <p><strong>Photo:</strong> <?php echo htmlspecialchars($photoFileName); ?></p>
             <?php if (!empty($place['photoFileName'])): ?>
    <img 
        src="uploads/<?php echo htmlspecialchars($place['photoFileName']); ?>" 
        alt="<?php echo htmlspecialchars($place['name']); ?>" 
        style="max-width: 100%; height: auto; border-radius: 10px;"
    >
<?php else: ?>
    <p><em>Photo not available.</em></p>
<?php endif; ?>


            
            <!-- Like Button -->
            <button class="like-button <?php echo $user_liked ? 'liked' : ''; ?>" data-place-id="<?php echo htmlspecialchars($place['id']); ?>">
                Like (<?php echo $like_count; ?>)
            </button>

            <!-- Display Comments -->
            <h4>Comments:</h4>
            <?php
            // Fetch comments for this place
            $comment_query = "SELECT c.comment, u.firstName, u.lastName FROM comment c
                              JOIN user u ON c.userID = u.id
                              WHERE c.placeID = ?;";
            $stmt = $conn->prepare($comment_query);
            $stmt->bind_param("i", $place['id']);
            $stmt->execute();
            $comment_result = $stmt->get_result();

            while ($comment = $comment_result->fetch_assoc()) {
                echo '<p><strong>' . htmlspecialchars($comment['firstName']) . ' ' . htmlspecialchars($comment['lastName']) . ':</strong> ' . htmlspecialchars($comment['comment']) . '</p>';
            }
            ?>

            <div class="add-comment">
    <form>
        <textarea name="comment_text" rows="4" placeholder="Add your comment..."></textarea>
        <input type="hidden" name="place_id" value="<?php echo htmlspecialchars($place['id']); ?>">
        <button type="button" class="submit-comment" data-place-id="<?php echo htmlspecialchars($place['id']); ?>">Post Comment</button>
    </form>
</div>
        </div>
        <script>
        $(document).ready(function () {
    $('.submit-comment').click(function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Retrieve the comment text and place ID from the form
        var button = $(this);
        var placeId = button.data('place-id'); // Get place_id from the button
        var commentText = button.closest('form').find('textarea[name="comment_text"]').val(); // Get the comment text

        // Check if the comment text is empty
        if (commentText.trim() === '') {
            alert('Please enter a comment.');
            return;
        }

        // Send the data via AJAX
        $.ajax({
            type: 'POST',
            url: 'comment.php', // Your PHP script to process the comment
            data: { place_id: placeId, comment_text: commentText },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // If successful, append the new comment to the comment section
                    var newComment = `
                        <p><strong>You:</strong> ${commentText}</p>
                    `;
                    // Find the place div and insert the new comment above the form
                    button.closest('.place').find('.add-comment').before(newComment);

                    // Clear the textarea after submitting
                    button.closest('form').find('textarea[name="comment_text"]').val('');
                } else {
                    alert('Failed to add comment.');
                }
            },
            error: function () {
                alert('An error occurred while submitting your comment.');
            },
        });
    });
});
    </script>

        <?php } ?>
    </div>
</body>
</html>
