<?php
session_start();

// Database configuration
$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];

// Set timezone to Malaysia time
date_default_timezone_set('Asia/Kuala_Lumpur');

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

// Handle form submission for adding review or reply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['review_content'])) {
    $review_content = $conn->real_escape_string($_POST['review_content']);
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;

    if (isset($_POST['rating'])) {
        $rating = floatval($_POST['rating']); // Convert rating to float
    } else {
        $rating = null; // Set rating to null if not provided (for seller or other cases)
    }

    $current_datetime = date('Y-m-d H:i:s');

    if (isset($_POST['edit_review_id'])) {
        // Update existing review
        $edit_review_id = intval($_POST['edit_review_id']);
        $update_sql = "UPDATE item_review SET review = '$review_content', rating = " . ($rating !== null ? "'$rating'" : "NULL") . ", date_created = '$current_datetime' WHERE id = '$edit_review_id' AND email = '$email'";

        if ($conn->query($update_sql) === TRUE) {
            $successMessage = "Review updated successfully.";
        } else {
            $errorMessage = "Error updating review: " . $conn->error;
        }
    } else {
        // Insert new review
        $insert_sql = "INSERT INTO item_review (fname, lname, email, review, rating, date_created, parent_id) 
                       VALUES ('$fname', '$lname', '$email', '$review_content', " . ($rating !== null ? "'$rating'" : "NULL") . ", '$current_datetime', " . ($parent_id !== null ? "'$parent_id'" : "NULL") . ")";

        if ($conn->query($insert_sql) === TRUE) {
            $successMessage = "Review added successfully.";
        } else {
            $errorMessage = "Error adding review: " . $conn->error;
        }
    }
}

// Fetch reviews from item_review table
$reviews_sql = "SELECT * FROM item_review ORDER BY date_created DESC";
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
        }

        .star {
            cursor: pointer;
            color: #ddd;
        }

        .star.filled {
            color: #000;
        }

        .reply-form {
            margin-left: 20px;
        }

        .replies {
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.html">
            <img src="images/shoesfitlogo.jpg" alt="ShoesFit Logo" style="height: 50px; margin-right: 10px;">ShoesFit
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                    // Fetch all reviews
                    $reviews = [];
                    while ($row = $reviews_result->fetch_assoc()) {
                        $reviews[] = $row;
                    }
                    // Function to display reviews and their replies recursively
                    function display_reviews($reviews, $parent_id = null, $depth = 0)
                    {
                        global $email;
                        foreach ($reviews as $review) {
                            if ($review['parent_id'] == $parent_id) {
                                echo '<div class="review card mb-3" style="margin-left: ' . (20 * $depth) . 'px;">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title"><strong>' . htmlspecialchars($review['fname']) . ' ' . htmlspecialchars($review['lname']) . '</strong></h5>';
                                if ($review['parent_id'] === NULL && $review['fname'] !== 'Seller') {
                                    echo '<div class="rating">';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            echo '<i class="fas fa-star filled"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    echo '</div>';
                                }
                                echo '<div class="col-auto text-right">';
                                echo '<p class="card-text"><small class="text-muted">Posted on ' . date('F j, Y, g:i a', strtotime($review['date_created'])) . '</small></p>';
                                echo '</div>';
                                echo '<p class="card-text">' . htmlspecialchars($review['review']) . '</p>';
                                if ($review['email'] == $email) {
                                    echo '<a href="#" class="edit-link" data-review-id="' . $review['id'] . '">Edit</a> | ';
                                }
                                echo '<a href="#" class="reply-link" data-review-id="' . $review['id'] . '">Reply</a>';
                                echo '</div>';
                                display_reviews($reviews, $review['id'], $depth + 1);
                                echo '</div>';
                            }
                        }
                    }
                    display_reviews($reviews);
                } else {
                    echo '<p>No reviews yet.</p>';
                }
                ?>
            </div>
            <hr>

            <!-- Review Form -->
            <form id="review-form" action="review.php" method="post" class="mt-4">
                <h3>Add Your Review</h3>
                <?php if ($fname !== 'Seller') : ?>
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
                <input type="hidden" name="edit_review_id" id="edit-review-id" value="">
            </form>
        </div>

        <!-- Reply Form Template -->
        <div id="reply-form-template" style="display: none;">
            <form class="reply-form" action="review.php" method="post">
                <h3>Reply</h3>
                <div class="form-group">
                    <label for="review-content">Your Reply:</label>
                    <textarea class="form-control" name="review_content" rows="3" required></textarea>
                </div>
                <input type="hidden" name="parent_id" value="">
                <button type="submit" class="btn btn-primary">Submit Reply</button>
            </form>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingValue = document.querySelector('#rating-value');
            const reviewForm = document.querySelector('#review-form');
            const ratingSection = document.querySelector('#rating');

            function setStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.querySelector('i').classList.remove('far');
                        star.querySelector('i').classList.add('fas');
                        star.querySelector('i').classList.add('filled');
                    } else {
                        star.querySelector('i').classList.remove('fas');
                        star.querySelector('i').classList.remove('filled');
                        star.querySelector('i').classList.add('far');
                    }
                });
            }

            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    const rating = index + 1;
                    ratingValue.value = rating;

                    setStars(rating);
                });
            });

            const fname = '<?php echo $fname; ?>';
            if (fname === 'Seller') {
                ratingSection.style.display = 'none'; // Hide rating if user is a seller
            }

            reviewForm.addEventListener('submit', function(event) {
                if (ratingSection.style.display === 'none') {
                    event.preventDefault();
                    alert('You are not allowed to submit a review with a rating.');
                }
            });

            document.querySelectorAll('.reply-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const replyFormTemplate = document.querySelector('#reply-form-template').cloneNode(true);
                    replyFormTemplate.style.display = 'block';
                    replyFormTemplate.querySelector('input[name="parent_id"]').value = reviewId;
                    this.parentNode.appendChild(replyFormTemplate);
                    this.style.display = 'none'; // Hide reply link after clicking
                });
            });

            document.querySelectorAll('.edit-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const reviewContent = this.parentNode.querySelector('.card-text').textContent;

                    document.querySelector('#review-content').value = reviewContent;
                    document.querySelector('#edit-review-id').value = reviewId;

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
        });
    </script>
</body>

</html>
