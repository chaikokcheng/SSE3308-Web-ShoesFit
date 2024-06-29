<?php

$errors = []; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $config = require 'config.php';

    $servername = $config['servername'];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $phoneNo = $conn->real_escape_string($_POST['phoneNo']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $con_password = $conn->real_escape_string($_POST['con_password']);

    $errors = array();

    if ($password !== $con_password) {
        $errors[] = "Passwords do not match!";
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        $errors[] = "Email already exists!";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO users (fname, lname, ph_num, dob, email, password, con_password) VALUES ('$fname', '$lname', '$phoneNo', '$birthDate', '$email', '$password', '$con_password')";


        if ($conn->query($sql) === TRUE) {
            $successMessage = "You are registered successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } 

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap"
        rel="stylesheet">
    <title>Register</title>

    <link rel="stylesheet" type="text/css" href="styles/main.css">
   
</head>

<body>
    <a class="login-logo d-flex justify-content-center align-items-center py-3" href="#">
        <img src="images/shoesfitlogo.jpg" alt="ShoesFit Logo" style="height: 100px; margin-right: 20px;">ShoesFit
    </a>

    <div class="col-6 mx-auto m-3 px-5 pb-5 pt-2 bg-light rounded-3 shadow">
        <h2 class="fs-6 fw-bold font-weight-bold mb-4">Register</h2>
        <div id="messages">
            <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger text-center">
                <?= $error ?>
            </div>
            <?php endforeach; ?>
            <?php elseif (!empty($successMessage)): ?>
            <div class="alert alert-success text-center">
                <?= $successMessage ?>
            </div>
            <?php endif; ?>
        </div>
        <form action="register.php" method="post">
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
                    <div class="form-floating mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>

                    <div class="form-floating mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control password hidden" name="password" id="password"
                            value="" required>
                    </div>
                    <div class="form-floating mb-4">
                        <label for="con_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control password hidden" name="con_password"
                            id="con_password" value="" required>
                    </div>


                    <div class="col-md-8 col-sm-10 mx-auto pt-2 ">

                        <input type="submit" class="btn col-12 shadow-sm" value="Register"
                            style="border-radius: 15px;" id="registerButton">

                        <h6 class="text-muted text-center" style="margin-top: 20%;">Already have an account? <a
                                href="login.php" id="loginPage">Login</a></h6>
                    </div>
                </div>
            </div>
        </form>
    </div>



</body>

</html>