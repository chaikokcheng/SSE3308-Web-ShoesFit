<?php
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

// Retrieve data from POST
$orderNumber = $_POST['orderNumber'];
$productName = $_POST['productName'];
$quantity = $_POST['quantity'];
$size = $_POST['size'];
$color = $_POST['color'];
$totalPrice = $_POST['totalPrice'];

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO order_history (order_number, product_name, quantity, size, color, total_price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $orderNumber, $productName, $quantity, $size, $color, $totalPrice);

// Execute the statement
if ($stmt->execute()) {
    echo "Order inserted successfully!";
} else {
    echo "Error: " . $conn->error;
}

// Close statement and connection
$stmt->close();
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="orderSummary.css">
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
    <form id="orderForm" method="POST" action="orderSummary.php">
        <div class="container mt-3" id="greeting">
            <h6>Order Number: <span id="orderNumber"></span></h6>
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
        <!-- Hidden inputs to capture order data -->
        <input type="hidden" id="orderNumber" name="orderNumber">
        <input type="hidden" id="productName" name="productName">
        <input type="hidden" id="quantity" name="quantity">
        <input type="hidden" id="size" name="size">
        <input type="hidden" id="color" name="color">
        <input type="hidden" id="totalPriceInput" name="totalPrice">
        <div class="col-md-11 col-12 mx-auto mb-lg-0 mb-5 orderedItems">
            <h1>ORDERED ITEMS</h1>
            <section id="orderedSection"></section>

        </div>
    </form>

    <div class="container py-5">
        <button type="button" , id="printBtn" class="btn btn-primary btn-sm pull-left">Print Your Order</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function generateOrderNumber() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let orderNumber = '';
            for (let i = 0; i < 8; i++) {
                orderNumber += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return orderNumber;
        }

        document.addEventListener("DOMContentLoaded", function () {
            const orderNumberElement = document.getElementById("orderNumber");
            const generatedOrderNumber = generateOrderNumber();
            orderNumberElement.innerText = generatedOrderNumber;

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
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const printBtn = document.getElementById("printBtn");
            if (printBtn) {
                printBtn.addEventListener("click", function () {
                    console.log("Print button clicked");
                    window.print();
                });
            }
        });
    </script>

    <script>
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
                    .then(response => response.json())
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

                        // Populate form hidden inputs with final data
                        document.getElementById("productName").value = cart[0].productName; // Assuming you store product name in cart
                        document.getElementById("quantity").value = cart[0].quantity; // Assuming you store quantity in cart
                        document.getElementById("size").value = cart[0].size; // Assuming you store size in cart
                        document.getElementById("color").value = cart[0].color; // Assuming you store color in cart
                        document.getElementById("totalPriceInput").value = (total + 10).toFixed(2); // Total price with shipping

                        // Automatically submit the form
                        document.getElementById("orderForm").submit();
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                    })
            }

        });
    </script>


</body>

</html>