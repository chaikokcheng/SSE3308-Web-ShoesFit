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
if (isset($_SESSION['email'])) {
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
}

// Handle form submission for adding review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_content = $conn->real_escape_string($_POST['review_content']);
    
    // Check if 'rating' is set in POST data
    if (isset($_POST['rating'])) {
        $rating = floatval($_POST['rating']); // Convert rating to float
    } else {
        $rating = null; // Set rating to null if not provided (for seller or other cases)
    }

    // Insert query for item_review including rating
    $insert_sql = "INSERT INTO item_review (fname, lname, email, review, rating, date_created) 
                   VALUES ('$fname', '$lname', '$email', '$review_content', " . ($rating !== null ? "'$rating'" : "NULL") . ", NOW())";

    if ($conn->query($insert_sql) === TRUE) {
        $successMessage = "Review added successfully.";
    } else {
        $errorMessage = "Error adding review: " . $conn->error;
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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="main.css">
    <style>
        .rating {
            display: inline-block;
            unicode-bidi: bidi-override;
            direction: rtl;
        }

        .rating .star {
            display: inline-block;
            padding: 5px;
            cursor: pointer;
        }

        .rating .star:hover {
            color: #ffc107; /* Change to desired hover color */
        }

        .rating .star.active {
            color: #ffc107; /* Change to desired active color */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.html">
            <img src="images/shoesfitlogo.jpg" alt="ShoesFit Logo" style="height: 50px; margin-right: 10px;">ShoesFit
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cart.html" title="Cart">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="User Profile">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main>
        <div id="reviews-section" class="container mt-3">
            <h2 style="padding-bottom: 3%;">Product Reviews</h2>

            <!-- Display existing reviews -->
            <div id="review-list">
                <?php
                if ($reviews_result->num_rows > 0) {
                    while ($row = $reviews_result->fetch_assoc()) {
                        echo '<div class="review card mb-3">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title"><strong>' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</strong></h5>';
                        // echo '<h6 class="card-subtitle mb-2 text-muted">' . htmlspecialchars($row['email']) . '</h6>';
                        echo '<div class="rating">';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $row['rating']) {
                                echo '<i class="fas fa-star"></i>'; // Use solid star icon for filled rating
                            } else {
                                echo '<i class="far fa-star"></i>'; // Use outline star icon for empty rating
                            }
                        }
                        echo '</div>';
                        echo '<div class="col-auto text-right">';
                        echo '<p class="card-text"><small class="text-muted">Posted on ' . date('F j, Y, g:i a', strtotime($row['date_created'])) . '</small></p>'; // Format and display date and time
                        echo '</div>'; 
                        echo '<p class="card-text">' . htmlspecialchars($row['review']) . '</p>';
                        echo '</div>';
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
                <h3>Add Your Review</h3>
                <?php if ($email !== 'seller@email.com'): ?>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <div id="rating" class="rating">
                        <span class="star" data-rating="1"><i class="far fa-star"></i></span>
                        <span class="star" data-rating="2"><i class="far fa-star"></i></span>
                        <span class="star" data-rating="3"><i class="far fa-star"></i></span>
                        <span class="star" data-rating="4"><i class="far fa-star"></i></span>
                        <span class="star" data-rating="5"><i class="far fa-star"></i></span>
                        <input type="hidden" name="rating" id="rating-value" required>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="review-content">Your Review:</label>
                    <textarea class="form-control" id="review-content" name="review_content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.star');
            const ratingValue = document.querySelector('#rating-value');
            const reviewForm = document.querySelector('#review-form');
            const ratingSection = document.querySelector('#rating');

            // Function to set stars based on rating (from database or user click)
            function setStars(rating) {
                stars.forEach((star, index) => {
                    const starIndex = stars.length - index - 1; // Calculate star index from right to left
                    if (starIndex < rating) {
                        star.innerHTML = '<i class="fas fa-star"></i>'; // Solid star for selected rating
                    } else {
                        star.innerHTML = '<i class="far fa-star"></i>'; // Outline star for non-selected rating
                    }
                });
            }

            // Retrieve and set initial rating from database (or default to 0 if no rating)
            const currentRating = <?php echo isset($_POST['rating']) ? json_encode($_POST['rating']) : '0'; ?>;
            setStars(currentRating);

            // Event listener for clicking on stars
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    const rating = stars.length - index; // Calculate rating from left to right
                    ratingValue.value = rating; // Update hidden input value

                    setStars(rating);
                });
            });

            // Hide rating section if seller is logged in
            const email = '<?php echo $email; ?>';
            if (email === 'seller@email.com') {
                ratingSection.style.display = 'none'; // Hide rating if seller is logged in
            }

            // Optional: You can also prevent form submission if rating section is hidden
            reviewForm.addEventListener('submit', function(event) {
                if (ratingSection.style.display === 'none') {
                    event.preventDefault(); // Prevent form submission
                    alert('You are not allowed to submit a review with a rating.');
                }
            });
        });
    </script>
</body>
</html>
