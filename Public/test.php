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
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $review_content = $conn->real_escape_string($_POST['review_content']);

    // Insert query for item_review
    $insert_sql = "INSERT INTO item_review (fname, lname, email, product_name, review) 
    VALUES ('$fname', '$lname', '$email', '$product_name', '$review_content')";

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
    <title>Nike Dunk Low Retro</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="main.css">
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

    <hr>
    <main>
        <div id="product-section" class="container py-5">
            <div class="row">
                <div id="product-images" class="col-2">
                    <img class="d-block w-100 product-thumb" src="images/sportNIkeDunkLow.png">
                    <img class="d-block w-100 product-thumb" src="images/sportAdidasOriginalsHB.png">
                    <img class="d-block w-100 product-thumb" src="images/sportNB530.png" alt="Image 3">
                    <img class="d-block w-100 product-thumb" src="images/heelJCAziaPump75.png">
                </div>
                <div id="product-image-main-container" class="col-6">
                </div>
                <div class="d-flex flex-column col-4">
                    <div id="product-details"></div>
                    <div class="quantity-control my-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <button id="decrease" class="btn btn-secondary">-</button>
                            </div>
                            <div class="col-auto">
                                <input id="quantity" type="text" class="form-control" value="1">
                            </div>
                            <div class="col-auto">
                                <button id="increase" class="btn btn-secondary">+</button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary add-to-cart">Add to Cart</button>
                </div>
            </div>
        </div>

        <hr>

        <div id="reviews-section" class="container mt-5">
            <h2>Product Reviews</h2>

            <!-- Display existing reviews -->
            <div id="review-list">
                <?php
                if ($reviews_result->num_rows > 0) {
                    while ($row = $reviews_result->fetch_assoc()) {
                        echo '<div class="review">';
                        echo '<p><strong>' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</strong></p>';
                        echo '<p>' . htmlspecialchars($row['email']) . '</p>';
                        echo '<p>Product: ' . htmlspecialchars($row['product_name']) . '</p>';
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
            <form id="review-form" action="test.php" method="post" class="mt-4">
    <h3>Add Your Review</h3>
    <div class="form-group">
        <label for="product-name">Product Name:</label>
        <input type="text" class="form-control" id="product-name" name="product_name" required>
    </div>
    <div class="form-group">
        <label for="review-content">Your Review:</label>
        <textarea class="form-control" id="review-content" name="review_content" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Review</button>
</form>
        </div>

        <hr>

        <div id="similar-products-section" class="container mt-5 pb-5">
            <h2>Similar Products</h2>
            <div id="similarProductsCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner"></div>
                <a class="carousel-control-prev" href="#similarProductsCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#similarProductsCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="main.js"></script>
</body>

</html>
