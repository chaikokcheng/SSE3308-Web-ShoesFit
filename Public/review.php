<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <style>
        .container {
            margin-top: 50px;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
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

    <div id="reviews-section" class="container mt-3">
    <h2 style="padding-bottom: 3%;">Product Reviews</h2>

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

        // Retrieve product ID from query parameter (make sure to sanitize this)
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;

        // Fetch product details from database based on $productId
        if ($productId) {
            $query = "SELECT * FROM products WHERE id = :productId";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                // Display product name and image
                echo "<h2>{$product['name']}</h2>";
                echo "<img src='{$product['img']}' alt='{$product['name']}' class='img-fluid'><br><br>";

                // Example: Display reviews for this product from 'item_review' table
                $reviews_sql = "SELECT ir.*, u.fname, u.lname FROM item_review ir 
                                JOIN users u ON ir.email = u.email 
                                WHERE ir.product_id = :productId
                                ORDER BY ir.date_created DESC";
                $stmt = $conn->prepare($reviews_sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $reviews_result = $stmt->get_result();

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
            } else {
                echo "Product not found.";
            }
        } else {
            echo "Invalid product ID.";
        }

        // Close connection
        $conn->close();
        ?>

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
            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
            <button type="submit" class="btn btn-primary">Submit Review</button>
            <input type="hidden" name="edit_review_id" id="edit-review-id" value="">
        </form>

        <div id="reply-form-template" style="display: none;">
            <form class="reply-form" action="review.php" method="post">
                <h3>Reply</h3>
                <div class="form-group">
                    <label for="review-content">Your Reply:</label>
                    <textarea class="form-control" name="review_content" rows="3" required></textarea>
                </div>
                <input type="hidden" name="parent_id" value="">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                <button type="submit" class="btn btn-primary">Submit Reply</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        // Client-side JavaScript for handling rating stars
        $(document).ready(function() {
            $('.star').click(function() {
                var rating = $(this).data('rating');
                $('#rating .star').removeClass('filled');
                $(this).prevAll().addBack().addClass('filled');
                $('#rating-value').val(rating);
            });

            // Edit link click handler
            $('.edit-link').click(function(e) {
                e.preventDefault();
                var reviewId = $(this).data('review-id');
                $('#edit-review-id').val(reviewId);
                var reviewContent = $(this).closest('.review').find('.card-text').text().trim();
                $('#review-content').val(reviewContent);
                $('#review-form-title').text('Edit Your Review');
                $('#review-form button[type="submit"]').text('Update Review');
                $('html, body').animate({
                    scrollTop: $('#review-form').offset().top
                }, 'slow');
            });

            // Delete link click handler
            $('.delete-link').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this review?')) {
                    var reviewId = $(this).data('review-id');
                    // Perform AJAX delete request
                    $.post('review.php', { delete_review_id: reviewId }, function(response) {
                        // Reload page or handle response as needed
                        location.reload();
                    });
                }
            });

            // Reply link click handler
            $('.reply-link').click(function(e) {
                e.preventDefault();
                var reviewId = $(this).data('review-id');
                $('#reply-form-template').appendTo($(this).closest('.review').find('.card-body')).show();
                $('input[name="parent_id"]').val(reviewId);
                $('html, body').animate({
                    scrollTop: $('#reply-form-template').offset().top
                }, 'slow');
            });
        });
    </script>
</body>
</html>
