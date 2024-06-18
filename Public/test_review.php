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

// Handle form submission for adding/editing/deleting review or reply
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['review_content'])) {
        $review_content = $conn->real_escape_string($_POST['review_content']);
        $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
        $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : NULL;
        $current_datetime = date('Y-m-d H:i:s');

        if (isset($_POST['edit_review_id']) && !empty($_POST['edit_review_id'])) {
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
    } elseif (isset($_POST['delete_review_id'])) {
        // Handle review deletion
        $delete_review_id = intval($_POST['delete_review_id']);
        $delete_sql = "DELETE FROM item_review WHERE id = '$delete_review_id' AND email = '$email'";

        if ($conn->query($delete_sql) === TRUE) {
            $successMessage = "Review deleted successfully.";
        } else {
            $errorMessage = "Error deleting review: " . $conn->error;
        }
    }
}

// Fetch reviews from item_review table
$reviews_sql = "SELECT ir.*, u.fname, u.lname FROM item_review ir 
                JOIN users u ON ir.email = u.email 
                ORDER BY ir.date_created DESC";
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
                                    echo '<a href="#" class="delete-link" data-review-id="' . $review['id'] . '">Delete</a> | ';
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
                <h3 id="review-form-title">Add Your Review</h3>
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
    </main>

    <footer class="bg-light text-center text-lg-start">
        <div class="text-center p-3">
            © 2023 ShoesFit. All rights reserved.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle star rating selection
            document.querySelectorAll('.star').forEach(star => {
                star.addEventListener('click', function() {
                    const ratingValue = this.getAttribute('data-rating');
                    document.getElementById('rating-value').value = ratingValue;
                    document.querySelectorAll('.star').forEach(s => {
                        s.classList.remove('filled');
                    });
                    this.classList.add('filled');
                    let previousSibling = this.previousElementSibling;
                    while (previousSibling) {
                        previousSibling.classList.add('filled');
                        previousSibling = previousSibling.previousElementSibling;
                    }
                });
            });

            // Handle edit review
            document.querySelectorAll('.edit-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const reviewText = this.closest('.card-body').querySelector('.card-text').innerText;
                    const ratingValue = this.closest('.card-body').querySelector('.filled') ? this.closest('.card-body').querySelectorAll('.filled').length : 0;

                    document.getElementById('review-content').value = reviewText;
                    document.getElementById('edit-review-id').value = reviewId;
                    document.getElementById('review-form-title').innerText = 'Edit Your Review';
                    if (ratingValue) {
                        document.getElementById('rating-value').value = ratingValue;
                        document.querySelectorAll('.star').forEach(s => {
                            s.classList.remove('filled');
                        });
                        for (let i = 1; i <= ratingValue; i++) {
                            document.querySelector(`.star[data-rating="${i}"]`).classList.add('filled');
                        }
                    }

                    // Scroll to the review form
                    document.getElementById('review-form').scrollIntoView({ behavior: 'smooth' });
                });
            });

            // Handle delete review
            document.querySelectorAll('.delete-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    if (confirm('Are you sure you want to delete this review?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'review.php';

                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'delete_review_id';
                        input.value = reviewId;

                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Handle reply to review
            document.querySelectorAll('.reply-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const reviewForm = document.getElementById('review-form');
                    const clonedForm = reviewForm.cloneNode(true);
                    clonedForm.classList.add('reply-form');
                    clonedForm.querySelector('h3').innerText = 'Reply to Review';
                    clonedForm.querySelector('button').innerText = 'Submit Reply';
                    clonedForm.querySelector('#edit-review-id').remove();
                    const parentIdInput = document.createElement('input');
                    parentIdInput.type = 'hidden';
                    parentIdInput.name = 'parent_id';
                    parentIdInput.value = reviewId;
                    clonedForm.appendChild(parentIdInput);
                    this.closest('.card').appendChild(clonedForm);
                    this.style.display = 'none';
                });
            });
        });
    </script>
</body>

</html>
