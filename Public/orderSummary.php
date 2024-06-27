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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $email = $user['email'];
    } else {
        die("User not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $productId = isset($_POST['productId']) ? intval($_POST['productId']) : NULL;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : NULL;
    $size = $conn->real_escape_string($_POST['size']);
    $color = $conn->real_escape_string($_POST['color']);
    $totalPrice = isset($_POST['totalPrice']) ? floatval($_POST['totalPrice']) : NULL;

    // Insert the order into the ORDERS table
    $orderDate = date('Y-m-d');
    $insertOrderSql = "INSERT INTO orders (cust_email, order_date, total_amount) VALUES (?, ?, ?)";
    $insertOrderStmt = $conn->prepare($insertOrderSql);
    if ($insertOrderStmt) {
        $insertOrderStmt->bind_param("ssd", $email, $orderDate, $totalPrice);
        if ($insertOrderStmt->execute()) {
            $orderId = $insertOrderStmt->insert_id;

            // Fetch product details from the products table
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();

                    // Insert the order details into the orders_details table
                    $insertOrderDetailSql = "INSERT INTO orders_details (order_id, product_id, qty, order_price) VALUES (?, ?, ?, ?)";
                    $insertOrderDetailStmt = $conn->prepare($insertOrderDetailSql);
                    if ($insertOrderDetailStmt) {
                        $orderPrice = $product['price'];
                        $insertOrderDetailStmt->bind_param("iiid", $orderId, $productId, $quantity, $orderPrice);
                        if ($insertOrderDetailStmt->execute()) {
                            echo "Order details inserted successfully.";
                        } else {
                            echo "Error inserting order details: " . $insertOrderDetailStmt->error;
                        }
                        $insertOrderDetailStmt->close();
                    } else {
                        echo "Error preparing order detail insert statement: " . $conn->error;
                    }
                } else {
                    echo "Product not found.";
                }
                $stmt->close();
            } else {
                echo "Error preparing select statement: " . $conn->error;
            }
        } else {
            echo "Error inserting order: " . $insertOrderStmt->error;
        }
        $insertOrderStmt->close();
    } else {
        echo "Error preparing order insert statement: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="orderSummary.css">
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
    <div class="container mt-3" id="summary-title">
        <h1>YOUR SHOES IS ON ITS WAY TO YOU</h1>
    </div>
    <br>
    <!-- <form id="orderForm" method="POST" action="orderSummary.php"> -->
    <div class="container mt-3" id="greeting">
        <!-- <h6>Order Number: SHF<span id="orderNumber"></span></h6> -->
        <h7>Dear custormer,</h7><br>
        <p>Big thanks for choosing ShoesFit! Your trust and support mean the world to us.
            We hope your new shoes bring you all the comfort and style you deserve.
            Thank you for being a part of the ShoesFit family. We look forward to serving you again soon!
        </p>
    </div>
    <br>
    <div class="container mt-3" id="summary">
        <h1>ORDER SUMMARY</h1>
        <div class="row">
            <div class="col-sm-4">
                <h5>DELIVERY ADDRESS</h5>
                <p id="fullAddress"></p>
            </div>
            <div class="col-sm-4">
                <h5>BILLING INFORMATION</h5>
                <p id="billingAddress"></p>
                <h6 id="paymentMethod"></h6>
            </div>
            <div class="col-sm-4">
                <h5>ORDER SUMMARY</h5>
                <div class="row">
                    <div class="col-6">
                        Product
                    </div>
                    <div class="col-6 text-right font-italic">
                        $ <span id="productAmount"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Promo Code
                    </div>
                    <div class="col-6 text-right">
                        <i>$ -0.00</i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Shipping
                    </div>
                    <div class="col-6 text-right" id="shippingCharge">
                        <i>$ 10.00</i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Total Amount
                    </div>
                    <div class="col-6 text-right">
                        $ <span id="totalPrice"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Total Saving
                    </div>
                    <div class="col-6 text-right">
                        <i>$ 0.00</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>



    <div class="col-md-11 col-12 mx-auto mb-lg-0 mb-5 orderedItems">
        <h1>ORDERED ITEMS</h1>
        <section id="orderedSection"></section>
    </div>

    <form id="orderForm" method="POST" action="orderSummary.php">
        <input type="hidden" name="productId" id="productId">
        <!-- <input type="hidden" name="orderNumber" id="orderNumber"> -->
        <input type="hidden" name="productName" id="productName">
        <input type="hidden" name="quantity" id="quantity">
        <input type="hidden" name="size" id="size">
        <input type="hidden" name="color" id="color">
        <input type="hidden" name="totalPrice" id="totalPrice">

        <div class="container py-5">
            <a href="orderHistory.php"><button type="submit" name="viewHistory" id="historyBtn" class="btn btn-primary btn-sm pull-left">View Your Order History</button>
        </div></a>
    </form>

    <div class="container py-5">
        <button type="button" , id="printBtn" class="btn btn-primary btn-sm pull-left">Print Your Order</button>
    </div>

    <!-- <div id="sidebar" class="sidebar">
        <a href="#" class="closebtn" id="closebtn"><span>CLOSE</span>&times;</a>
        <a href="profile.php" id="profile">Profile</a>
        <a href="orderHistory.php" id="orderHistory">Order History</a>
        <a href="login.php" id="logout"><span>LOGOUT</span></a>
    </div> -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // function generateOrderNumber() {
        //     var orderNumber = Math.floor(Math.random() * 1000000);
        //     return orderNumber;
        // }

        document.addEventListener("DOMContentLoaded", function() {
            // var orderNumber = generateOrderNumber();

            // document.getElementById("orderNumber").value = orderNumber;

            const streetAddress = localStorage.getItem("streetAddress");
            const city = localStorage.getItem("city");
            const state = localStorage.getItem("state");
            const postcode = localStorage.getItem("postcode");

            let fullAddress = '';
            if (streetAddress) fullAddress += streetAddress;
            if (city) fullAddress += `, <br> ${city}`;
            if (postcode) fullAddress += `,<br> ${postcode}`;
            if (state) fullAddress += `, ${state}`;

            document.getElementById("fullAddress").innerHTML = fullAddress;
            document.getElementById("billingAddress").innerHTML = fullAddress;
        });

        document.addEventListener("DOMContentLoaded", function() {
            const printBtn = document.getElementById("printBtn");
            if (printBtn) {
                printBtn.addEventListener("click", function() {
                    console.log("Print button clicked");
                    window.print();
                });
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const orderedSection = document.getElementById("orderedSection");
            const productAmt = document.getElementById("productAmount");
            const totalPrice = document.getElementById("totalPrice");
            const shippingCharge = 10;
            let total = 0;

            const cart = localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [];
            $('#total_items_count').text(cart.length);

            if (cart.length === 0) {
                orderedSection.innerHTML = '<p>Your cart is empty.</p>';
            } else {
                fetch('products.json')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const cartHTML = cart.map(cartItem => {
                            const product = data.products.find(p => p.id === cartItem.productId);
                            if (product) {
                                total += product.price * cartItem.quantity;
                                return `
                            <div class="card p-3 shadow" id="ordered-items">
        <div class="row">
            <div class="col-md-5 col-12 mx-auto d-flex justify-content-center align-items-center product_img">
                <img src="${product.img}" alt="${product.name}" class="shoeImage-size" width="100%" , height="auto" >
            </div>
            <div class="col-md-7 col-12 mx-auto px-4 mt-2 d-flex flex-column">
                <div class="row">
                    <div class="col-8 card-body">
                        <h4 class="mb-3 card-title text-align-left" style:"font-weight:900">${product.name}</h4>
                        <p class="shoe-colour">${cartItem.color}</p>
                    <div class="cart-text">
                        <p>Qty: ${cartItem.quantity}</p>
                        <p>Size: ${cartItem.size}</p>
                    </div>
                    </div>
                </div>
                <div class="row mt-auto">
                    <div class="col-12 d-flex justify-content-end align-items-end">
                        <br>
                        <h3 style="font-size: 30px, font-weight:bold">$<span id="itemval">${product.price * cartItem.quantity}</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            `;
                            }
                            return '';
                        }).join('');

                        orderedSection.innerHTML = cartHTML;
                        productAmt.innerText = total.toFixed(2);
                        totalPrice.innerText = (total + shippingCharge).toFixed(2);

                        if (cart.length > 0) {
                            document.getElementById("productId").value = cart[0].productId;
                            document.getElementById("productName").value = cart[0].productName;
                            document.getElementById("quantity").value = cart[0].quantity;
                            document.getElementById("size").value = cart[0].size;
                            document.getElementById("color").value = cart[0].color;
                            document.getElementById("totalPrice").value = (total + 10).toFixed(2);
                            // document.getElementById("orderNumber").innerText = Math.floor(Math.random() * 1000000);
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                        orderedSection.innerHTML = '<p>There was an error loading the products. Please try again later.</p>';
                    });

            }
        });
    </script>

    <!-- <script>
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
    </script> -->


</body>

</html>