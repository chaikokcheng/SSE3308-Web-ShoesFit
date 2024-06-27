<?php
session_start();
$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Retrieve order history
$sql = "SELECT o.order_id, o.order_date, od.qty, od.order_price, p.name, p.img 
        FROM orders o 
        JOIN orders_details od ON o.order_id = od.order_id 
        JOIN products p ON od.product_id = p.id 
        WHERE o.cust_email = '$email'";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="main.css">
    <style>
        #history-title h1 {
            font-weight: 800;
            letter-spacing: -3px;
        }

        .product-img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
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

    <div class="container mt-3" id="history-title">
        <h1>ORDER HISTORY</h1>
        <?php foreach ($orders as $order) { ?>
            <div class="row">
                <div class="card px-5 my-3">
                    <div class="row mt-auto">
                        <div class="col-4">
                            <img src="<?php echo htmlspecialchars($order['img']); ?>" alt="<?php echo htmlspecialchars($order['name']); ?>" class="product-img">
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <h5 class="card-title">Order Number: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                                <p class="card-text">Product Name: <?php echo htmlspecialchars($order['name']); ?></p>
                                <p class="card-text">Quantity: <?php echo htmlspecialchars($order['qty']); ?></p>
                                <p class="card-text">Price per Item: $<?php echo htmlspecialchars($order['order_price']); ?></p>
                                <p class="card-text">Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
                                <p class="card-text">Total Price: $<?php echo htmlspecialchars($order['qty'] * $order['order_price']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
