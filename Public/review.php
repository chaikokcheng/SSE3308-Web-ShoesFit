<?php
session_start();

// Database configuration
$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$fname = '';
$lname = '';
$email = '';
$successMessage = '';
$errorMessage = '';

// Fetch user data based on session email
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $fname = $user['fname'];
    $lname = $user['lname'];
    $email = $user['email'];
    
} else {
    die("User not found."); 
}

// Handle form submission for updating review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_content = $conn->real_escape_string($_POST['review_content']);

    // Update query for item_review
    $update_sql = "UPDATE item_review SET fname = '$fname', lname = '$lname', email = '$email', review = '$review_content' 
                   WHERE email = '$email'";

    if ($conn->query($update_sql) === TRUE) {
        $successMessage = "Review updated successfully.";
    } else {
        $errorMessage = "Error updating review: " . $conn->error;
    }
}

// Fetch reviews from item_review table
$reviews_sql = "SELECT * FROM item_review";
$reviews_result = $conn->query($reviews_sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link rel="stylesheet" type="text/css" href="main.css">
    <!-- Include any necessary JavaScript for star rating if not already included -->
</head>
<body>

    <div id="reviews-section" class="container mt-5">
        <h2>Product Reviews</h2>

        <?php
        // Display success or error message if set
        if (!empty($successMessage)) {
            echo '<div class="alert alert-success">' . $successMessage . '</div>';
        } elseif (!empty($errorMessage)) {
            echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
        }
        ?>

        <div id="review-list">
            <?php
            if ($reviews_result->num_rows > 0) {
                while ($row = $reviews_result->fetch_assoc()) {
                    echo '<div class="review">';
                    echo '<p><strong>' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</strong></p>'. ' '. htmlspecialchars($row['email']) . '</strong></p>';
                    echo '<p>' . htmlspecialchars($row['review']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No reviews yet.</p>';
            }
            ?>
        </div>
        <hr>

        <!-- Review Form -->
        <form id="review-form" action="review.php" method="post" class="mt-4">
            <h3>Update Your Review</h3>
            <div class="form-group">
                <label for="review-content">Your Review:</label>
                <textarea class="form-control" id="review-content" name="review_content" rows="3"><?php echo htmlspecialchars($review_content); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Review</button>
        </form>
    </div>

    <!-- Include any necessary JavaScript for star rating functionality if not already included -->

</body>
</html>
