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
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
    <h1>Welcome, <?php echo $fname . ' ' . $lname; ?></h1>
    
    <div id="messages">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $errorMessage; ?>
            </div>
        <?php elseif (isset($successMessage)): ?>
            <div class="alert alert-success text-center">
                <?php echo  $successMessage; ?>
            </div>
        <?php endif; ?>
    </div>

    <form action="profile.php" method="post">
        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" value="<?php echo $fname; ?>" >
        <br>
        
        <label for="lname">Last Name:</label>
        <input type="text" id="lname" name="lname" value="<?php echo $lname; ?>" >
        <br>
        
        <label for="phoneNo">Phone Number:</label>
        <input type="tel" id="phoneNo" name="phoneNo" value="<?php echo $phoneNo; ?>">
        <br>
        
        <label for="birthDate">Date of Birth:</label>
        <input type="date" id="birthDate" name="birthDate" value="<?php echo $birthDate; ?>" >
        <br>
        
        <button type="submit">Update Information</button>
    </form>

</body>
</html>
