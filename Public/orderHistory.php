<?php
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

// Fetch order history
$sql = "SELECT * FROM order_history ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="orderHistory.css">
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
                    <a class="nav-link" href="orderHistory.php" title="Order History">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-3">
        <h1>Order History</h1>
        <div class="row">
            <div class="col-12">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $orderedItems = json_decode($row['ordered_items'], true);
                        echo '<div class="card mb-3">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">Order Number: ' . $row['order_number'] . '</h5>';
                        echo '<p class="card-text"><strong>Order Date:</strong> ' . $row['order_date'] . '</p>';
                        echo '<p class="card-text"><strong>Full Address:</strong> ' . $row['full_address'] . '</p>';
                        echo '<p class="card-text"><strong>Billing Address:</strong> ' . $row['billing_address'] . '</p>';
                        echo '<p class="card-text"><strong>Payment Method:</strong> ' . $row['payment_method'] . '</p>';
                        echo '<p class="card-text"><strong>Product Amount:</strong> $' . $row['product_amount'] . '</p>';
                        echo '<p class="card-text"><strong>Shipping Charge:</strong> $' . $row['shipping_charge'] . '</p>';
                        echo '<p class="card-text"><strong>Total Price:</strong> $' . $row['total_price'] . '</p>';
                        echo '<h5>Ordered Items:</h5>';
                        echo '<ul>';
                        foreach ($orderedItems as $item) {
                            echo '<li>' . $item['name'] . ' - ' . $item['quantity'] . ' x $' . $item['price'] . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No order history found.</p>';
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>

</html>