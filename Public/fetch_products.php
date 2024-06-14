<?php
header('Content-Type: application/json');

$config = require 'config.php';

$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, category, description, price, img FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'description' => $row['description'],
            'price' => $row['price'],
            'img' => $row['img']
        ];
    }

    $conn->close();
    
    echo json_encode(['products' => $products]);
} else {
    echo json_encode(['error' => 'No products found']);
}

?>