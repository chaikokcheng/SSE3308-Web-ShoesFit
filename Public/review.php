<?php
session_start();

$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];

date_default_timezone_set('Asia/Kuala_Lumpur');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fname = '';
$lname = '';
$email = '';
$successMessage = '';
$errorMessage = '';

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

$productId = isset($_GET['id']) ? intval($_GET['id']) : null;


$product = null;
if ($productId) {
    $product_sql = "SELECT * FROM products WHERE id = $productId";
    $product_result = $conn->query($product_sql);
    if ($product_result->num_rows == 1) {
        $product = $product_result->fetch_assoc();
    } else {
        die("Product not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['review_content'])) {
        $review_content = $conn->real_escape_string($_POST['review_content']);
        $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : NULL;
        $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : NULL;
        $current_datetime = date('Y-m-d H:i:s');

        if (isset($_POST['edit_review_id']) && !empty($_POST['edit_review_id'])) {
            $edit_review_id = intval($_POST['edit_review_id']);
            $update_sql = "UPDATE item_review SET review = '$review_content', rating = " . ($rating !== null ? "'$rating'" : "NULL") . ", date_created = '$current_datetime' WHERE id = '$edit_review_id' AND email = '$email'";

            if ($conn->query($update_sql) === TRUE) {
                $successMessage = "Review updated successfully.";
            } else {
                $errorMessage = "Error updating review: " . $conn->error;
            }
        } else {
            $insert_sql = "INSERT INTO item_review (fname, lname, email, review, rating, date_created, parent_id, product_id) 
                           VALUES ('$fname', '$lname', '$email', '$review_content', " . ($rating !== null ? "'$rating'" : "NULL") . ", '$current_datetime', " . ($parent_id !== null ? "'$parent_id'" : "NULL") . ", '$product_id')";

            if ($conn->query($insert_sql) === TRUE) {
                $successMessage = "Review added successfully.";
            } else {
                $errorMessage = "Error adding review: " . $conn->error;
            }
        }
    } elseif (isset($_POST['delete_review_id'])) {
        $delete_review_id = intval($_POST['delete_review_id']);
        $delete_sql = "DELETE FROM item_review WHERE id = '$delete_review_id' AND email = '$email'";

        if ($conn->query($delete_sql) === TRUE) {
            $successMessage = "Review deleted successfully.";
        } else {
            $errorMessage = "Error deleting review: " . $conn->error;
        }
    }
}

$reviews_sql = "SELECT * FROM item_review WHERE product_id = $productId ORDER BY date_created DESC";
$reviews_result = $conn->query($reviews_sql);

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

        .product-img {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .product-name {
            text-align: center;
            margin-bottom: 20px;
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
                    <a class="nav-link"  title="User Profile" id="profile-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main>
        <div id="reviews-section" class="container mt-3">
            <?php if ($product) : ?>
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                <h2 style="padding-bottom: 3%;">Product Reviews</h2>
            <?php else : ?>
                <h2 style="padding-bottom: 3%;">Product Reviews</h2>
            <?php endif; ?>


            <div id="review-list">
                <?php
                if ($reviews_result->num_rows > 0) {

                    $reviews = [];
                    while ($row = $reviews_result->fetch_assoc()) {
                        $reviews[] = $row;
                    }

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
                                    echo '<a href="#" class="edit-link" data-review-id="' . $review['id'] . '" data-review-content="' . htmlspecialchars($review['review']) . '" data-review-rating="' . $review['rating'] . '">Edit</a> | ';
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

            <form id="review-form" action="review.php?id=<?php echo $productId; ?>" method="post" class="mt-4">
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
                <input type="hidden" name="edit_review_id" id="edit-review-id" value="">
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        </div>

        <div id="reply-form-template" style="display: none;">
            <form class="reply-form" action="review.php?id=<?php echo $productId; ?>" method="post">
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
    </main>
    <div id="sidebar" class="sidebar">
        <a href="#" class="closebtn" id="closebtn"><span>CLOSE</span>&times;</a>
        <a href="profile.php" id="profile">Profile</a>
        <a href="orderHistory.php"id="orderHistory">Order History</a>
        <a href="login.php" id="logout"><span>LOGOUT</span></a>
    </div>

    <br><br>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            document.querySelectorAll('.edit-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const reviewContent = this.getAttribute('data-review-content');
                    const reviewRating = this.getAttribute('data-review-rating');

                    document.getElementById('review-content').value = reviewContent;
                    document.getElementById('edit-review-id').value = reviewId;
                    document.getElementById('review-form-title').innerText = 'Edit Your Review';

                    document.querySelectorAll('.star').forEach(star => {
                        star.classList.remove('filled');
                    });
                    for (let i = 1; i <= reviewRating; i++) {
                        document.querySelector(`.star[data-rating="${i}"]`).classList.add('filled');
                    }
                    document.getElementById('rating-value').value = reviewRating;

                    window.scrollTo({
                        top: document.getElementById('review-form').offsetTop,
                        behavior: 'smooth'
                    });
                });
            });

            document.querySelectorAll('.delete-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    if (confirm('Are you sure you want to delete this review?')) {
                        $.post('review.php?id=<?php echo $productId; ?>', {
                            delete_review_id: reviewId
                        }, function(response) {
                            location.reload();
                        });
                    }
                });
            });

            document.querySelectorAll('.reply-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const reviewId = this.getAttribute('data-review-id');
                    const replyFormTemplate = document.querySelector('#reply-form-template').cloneNode(true);
                    replyFormTemplate.style.display = 'block';
                    replyFormTemplate.querySelector('input[name="parent_id"]').value = reviewId;
                    this.parentNode.appendChild(replyFormTemplate);
                    this.style.display = 'none';
                });
            });
        });
        document.getElementById("profile-icon").onclick = function () {
        var sidebar = document.getElementById("sidebar");
        if (sidebar.style.width === "200px") {
            sidebar.style.width = "0";
        } else {
            sidebar.style.width = "200px";
        }
    }

    document.getElementById("closebtn").onclick = function () {
        document.getElementById("sidebar").style.width = "0";
    }
    </script>
</body>

</html>