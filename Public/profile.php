<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

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

$email = $_SESSION['email'];

// Fetch user data based on email
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $fname = $user['fname'];
    $lname = $user['lname'];
    $phoneNo = $user['ph_num'];
    $birthDate = $user['dob'];
} else {
    die("User not found."); // Handle case where user is not found, although it should ideally not happen
}

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and escape inputs
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $phoneNo = $conn->real_escape_string($_POST['phoneNo']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);

    // Update query
    $update_sql = "UPDATE users SET fname = '$fname', lname = '$lname', ph_num = '$phoneNo', dob = '$birthDate' WHERE email = '$email'";

    if ($conn->query($update_sql) === TRUE) {
        $successMessage = "Profile updated successfully.";
    } else {
        $errorMessage = "Error updating profile: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
                    <a class="nav-link" title="User Profile" id="profile-icon">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-3" id="profile_title">
        <h1>Welcome, <?php echo $fname . ' ' . $lname; ?></h1>
    </div>

    <div id="messages">
        <?php if (isset($errorMessage)) : ?>
            <div class="alert alert-danger text-center">
                <?php echo $errorMessage; ?>
            </div>
        <?php elseif (isset($successMessage)) : ?>
            <div class="alert alert-success text-center">
                <?php echo  $successMessage; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-8 mx-auto m-3 px-5 pb-5 pt-2 bg-light rounded-3 shadow">
        <div class="row">
            <div class="col-4 my-auto">
                <img src="images/profile_pic.jpg" class="rounded-circle shadow-4" style="width: 200px;" alt="Avatar" />
            </div>
            <div class="col-8">
                <form action="profile.php" method="post">
                    <div class="row gy-2 overflow-hidden">
                        <div class="col-12">
                            <div class="row">
                                <div class="form-floating mb-3 col-6">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="fname" id="fname" required>
                                </div>
                                <div class="form-floating mb-3 col-6">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="lname" id="lname" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-floating mb-3 col-6">
                                    <label for="phoneNo" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phoneNo" id="phoneNo" required>
                                </div>
                                <div class="form-floating mb-3 col-6">
                                    <label for="birthDate" class="form-label">Date of Birth (dd/mm/yyyy)</label>
                                    <input type="date" class="form-control" name="birthDate" id="birthDate" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-secondary">Update Information</button>
                        </div>
                </form>
            </div>
        </div>

</body>

</html>