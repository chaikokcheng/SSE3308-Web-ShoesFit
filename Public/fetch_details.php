<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $config = require 'config.php';
    $servername = $config['servername'];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']);

        $product = array();

        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            $sql = "SELECT * FROM product_colors WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $colors = $result->fetch_all(MYSQLI_ASSOC);
            $product['colors'] = $colors;

            $sql = "SELECT * FROM product_sizes WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $sizes = $result->fetch_all(MYSQLI_ASSOC);
            $product['sizes'] = $sizes;

            header('Content-Type: application/json');
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'No product ID provided']);
    }

    $conn->close();
}
?>
