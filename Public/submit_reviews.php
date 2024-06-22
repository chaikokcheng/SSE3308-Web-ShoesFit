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
$email = $_SESSION['email']; // Assuming user email is stored in session
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_review_id'])) {
        $review_id = intval($_POST['delete_review_id']);
        $delete_sql = "DELETE FROM item_review WHERE id = ? AND email = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param('is', $review_id, $email);

        if ($stmt->execute()) {
            $successMessage = "Review deleted successfully.";
        } else {
            $errorMessage = "Error deleting review: " . $conn->error;
        }

        $stmt->close();
    } else {
        $review_content = $conn->real_escape_string($_POST['review_content']);
        $product_id = intval($_POST['product_id']);
        $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;
        $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        $current_datetime = date('Y-m-d H:i:s');

        if (isset($_POST['edit_review_id']) && !empty($_POST['edit_review_id'])) {
            $edit_review_id = intval($_POST['edit_review_id']);
            $update_sql = "UPDATE item_review SET review = ?, rating = ?, date_created = ? WHERE id = ? AND email = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param('sdiss', $review_content, $rating, $current_datetime, $edit_review_id, $email);

            if ($stmt->execute()) {
                $successMessage = "Review updated successfully.";
            } else {
                $errorMessage = "Error updating review: " . $conn->error;
            }

            $stmt->close();
        } else {
            $insert_sql = "INSERT INTO item_review (fname, lname, email, review, rating, product_id, parent_id, date_created) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param('ssssdiss', $_SESSION['fname'], $_SESSION['lname'], $email, $review_content, $rating, $product_id, $parent_id, $current_datetime);

            if ($stmt->execute()) {
                $successMessage = "Review added successfully.";
            } else {
                $errorMessage = "Error adding review: " . $conn->error;
            }

            $stmt->close();
        }
    }
}

// Redirect back to the review page for the specific product
header("Location: review.php?id=$product_id");
exit();
