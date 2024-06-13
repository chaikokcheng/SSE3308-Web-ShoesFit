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
