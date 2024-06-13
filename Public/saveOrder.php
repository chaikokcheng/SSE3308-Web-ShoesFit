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
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

/// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// if (json_last_error() !== JSON_ERROR_NONE) {
//     die("Error decoding JSON: " . json_last_error_msg());
// }
// // Debugging
// error_log("Received Data: " . print_r($orderDetails, true));

// Extract order details
// $order_number = $conn->real_escape_string($orderDetails['orderNumber']);
// $product_amount = $conn->real_escape_string($orderDetails['productAmount']);
// $shipping_charge = $conn->real_escape_string($orderDetails['shippingCharge']);
// $total_price = $conn->real_escape_string($orderDetails['totalPrice']);
// $ordered_items = $conn->real_escape_string(json_encode($orderDetails['orderedItems']));

$orderNumber = $conn->real_escape_string($data['orderNumber']);
$productNames = $conn->real_escape_string($data['productName']);
$quantities = $conn->real_escape_string($data['quantity']);
$colors = $conn->real_escape_string($data['color']);
$sizes = $conn->real_escape_string($data['size']);
$shippingCharge = $conn->real_escape_string($data['shippingCharge']);
$totalAmount = $conn->real_escape_string($data['totalAmount']);


$sql = "INSERT INTO order_history (order_number, product_name, quantity, color, size, shipping_charge, total_price)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $orderNumber, $productNames, $quantities, $colors, $sizes, $shippingCharge, $totalAmount);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Order saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
