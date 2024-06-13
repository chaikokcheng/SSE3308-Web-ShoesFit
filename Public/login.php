<?php
session_start();
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

    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);


    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['email'] = $user['email'];
            header("Location: index.html");
            exit;
        } else {
            $errors[] = "Password incorrect!";
        }
    } else {
        $errors[] = "Email does not exist!";
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
    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="main.css">
    <style>
       
    </style>
</head>

<body>

    <a class="login-logo d-flex justify-content-center align-items-center py-3" href="#">
        <img src="images/shoesfitlogo.jpg" alt="ShoesFit Logo" style="height: 100px; margin-right: 20px;">ShoesFit
    </a>

    <div class="col-5 mx-auto m-5 px-5 pb-5 pt-2 bg-light rounded-3 shadow">
        <h2 class="fs-6 fw-bold font-weight-bold mb-4">Log In</h2>
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger text-center">
                    <?= $error ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="row gy-2 overflow-hidden">
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email"
                            placeholder="Enter Email" required>
                    </div>

                    <div class="form-floating mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control password hidden" name="password" id="password"
                            value="" placeholder="Enter Password" required>
                    </div>


                    <div class="col-md-8 col-sm-10 mx-auto pt-2 ">

                        
                        <input type="submit" class="btn col-12 shadow-sm" value="login" style="border-radius: 15px;" id="loginButton">
                        

                        <!-- <a href="index.html"><button type="button" class="btn col-12 shadow-sm" style="border-radius: 15px;">login</button></a> -->

                        <h6 class="text-muted text-center" style="margin-top: 20%;">Haven't registered? <a href="register.php"
                                id="registerPage">Register</a></h6>
                    </div>
                </div>
            </div>
        </form>
    </div>



</body>

</html>