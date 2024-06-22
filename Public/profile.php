<?php
session_start();


if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit();
}


$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];


$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $fname = $user['fname'];
    $lname = $user['lname'];
    $phoneNo = $user['ph_num'];
    $birthDate = $user['dob'];
} else {
    die("User not found."); 
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $phoneNo = $conn->real_escape_string($_POST['phoneNo']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);

   
    $update_sql = "UPDATE users SET fname = '$fname', lname = '$lname', ph_num = '$phoneNo', dob = '$birthDate' WHERE email = '$email'";

    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['successMessage'] = "Profile updated successfully.";
        header('Location: profile.php');
        exit();
    } else {
        $errorMessage = "Error updating profile: " . $conn->error;
    }
}


$isEdit = isset($_GET['edit']) ? true : false;


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

    <div class="container mt-3" id="profile_title">
        <h1>Welcome, <?php echo $fname . ' ' . $lname; ?></h1>
    </div>

    <div id="messages">
        <?php if (isset($errorMessage)) : ?>
            <div class="alert alert-danger text-center">
                <?php echo $errorMessage; ?>
            </div>
        <?php elseif (isset($_SESSION['successMessage'])) : ?>
            <div class="alert alert-success text-center">
                <?php 
                    echo $_SESSION['successMessage']; 
                    unset($_SESSION['successMessage']);
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-8 mx-auto m-3 px-5 pb-5 pt-2 bg-light rounded-3 shadow">
        <div class="row">
            <div class="col-4 my-auto">
                <img src="images/profile_pic.jpg" class="rounded-circle shadow-4" style="width: 200px;" alt="Avatar" />
            </div>
            <div class="col-8">
                <form action="profile.php?edit=1" method="post">
                    <div class="row gy-2 overflow-hidden">
                        <div class="col-12">
                            <div class="row">
                                <div class="form-floating mb-3 col-6">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="fname" id="fname" value="<?php echo htmlspecialchars($fname); ?>" <?php echo $isEdit ? '' : 'readonly'; ?> required>
                                </div>
                                <div class="form-floating mb-3 col-6">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="lname" id="lname" value="<?php echo htmlspecialchars($lname); ?>" <?php echo $isEdit ? '' : 'readonly'; ?> required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-floating mb-3 col-6">
                                    <label for="phoneNo" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phoneNo" id="phoneNo" value="<?php echo htmlspecialchars($phoneNo); ?>" <?php echo $isEdit ? '' : 'readonly'; ?> required>
                                </div>
                                <div class="form-floating mb-3 col-6">
                                    <label for="birthDate" class="form-label">Date of Birth (dd/mm/yyyy)</label>
                                    <input type="date" class="form-control" name="birthDate" id="birthDate" value="<?php echo htmlspecialchars($birthDate); ?>" <?php echo $isEdit ? '' : 'readonly'; ?> required>
                                </div>
                            </div>

                            <?php if ($isEdit) : ?>
                                <button type="submit" name="update" class="btn btn-secondary">Update Information</button>
                            <?php else : ?>
                                <a href="profile.php?edit=1" class="btn btn-primary">Edit Profile</a>
                            <?php endif; ?>
                        </div>
                </form>
            </div>
        </div>
        <div id="sidebar" class="sidebar">
        <a href="#" class="closebtn" id="closebtn"><span>CLOSE</span>&times;</a>
        <a href="profile.php" id="profile">Profile</a>
        <a href="orderHistory.php"id="orderHistory">Order History</a>
        <a href="login.php" id="logout"><span>LOGOUT</span></a>
    </div>
    <br><br>
    <script>
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
