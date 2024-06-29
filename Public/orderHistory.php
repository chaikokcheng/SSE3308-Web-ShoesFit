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
$sql = "SELECT o.order_id, o.order_date, o.total_amount, od.qty, od.order_price, od.color, od.size, p.name, p.img 
        FROM orders o 
        JOIN orders_detail od ON o.order_id = od.order_id 
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
    <link rel="stylesheet" type="text/css" href="styles/main.css">
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

        .order-details {
            display: none;
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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $lastOrderId = null;
                foreach ($orders as $order) {
                    if ($lastOrderId !== $order['order_id']) {
                        if ($lastOrderId !== null) {
                            echo '</td></tr>';
                        }
                        $lastOrderId = $order['order_id'];
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                            <td>
                                <button class="btn btn-primary view-items-btn" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>">View Items</button>
                            </td>
                        </tr>
                        <tr class="order-details" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>">
                            <td colspan="4">
                                <div class="card px-5 my-3">
                                <?php
                            } ?>
                                <div class="row mt-auto">
                                    <div class="col-4 m-3">
                                        <img src="<?php echo htmlspecialchars($order['img']); ?>" alt="<?php echo htmlspecialchars($order['name']); ?>" class="product-img">
                                    </div>
                                    <div class="col-6">
                                        <div class="card-body">
                                            <h5 class="card-title">Product Name: <?php echo htmlspecialchars($order['name']); ?></h5>
                                            <p class="card-text">Quantity: <?php echo htmlspecialchars($order['qty']); ?></p>
                                            <p class="card-text">Color: <?php echo htmlspecialchars($order['color']); ?></p>
                                            <p class="card-text">Size: <?php echo htmlspecialchars($order['size']); ?></p>
                                            <p class="card-text">Price per Item: $<?php echo htmlspecialchars($order['order_price']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            if (end($orders) === $order) {
                                echo '</td></tr>';
                            }
                        }
                            ?>
                            </td>
                        </tr>
            </tbody>
        </table>
    </div>

    <div id="sidebar" class="sidebar">
        <a href="#" class="closebtn" id="closebtn"><span>CLOSE</span>&times;</a>
        <a href="profile.php" id="profile">Profile</a>
        <a href="orderHistory.php" id="orderHistory">Order History</a>
        <a href="login.php" id="logout"><span>LOGOUT</span></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.view-items-btn').on('click', function() {
                var orderId = $(this).data('order-id');
                $('.order-details[data-order-id="' + orderId + '"]').toggle();
            });
        });

        document.getElementById("profile-icon").onclick = function() {
            var sidebar = document.getElementById("sidebar");
            if (sidebar.style.width === "200px") {
                sidebar.style.width = "0";
            } else {
                sidebar.style.width = "200px";
            }
        }

        document.getElementById("closebtn").onclick = function() {
            document.getElementById("sidebar").style.width = "0";
        }
    </script>
</body>

</html>