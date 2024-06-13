<?php
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
        if(!isset($_GET['product_id'])){
            $response =array ('success'=>false,'error'=> 'Product ID is required');
            echo json_encode($response);
            exit;
        }
        
        $product_id = $_GET['product_id'];
        $sql = "select r.id, r.product_id, r.parent_id,u.lname as user_name, r.rating, r.content, r.date_created
                from reviews r
                join users u on r.user_email=u.email
                where r.product_id =$product_id
                order by r.date_created desc";

                if ($result) {
                    $reviews = array();
                    while ($row = $result->fetch_assoc()) {
                        $reviews[] = array(
                            'id' => $row['id'],
                            'product_id' => $row['product_id'],
                            'parent_id' => $row['parent_id'],
                            'user_name' => $row['user_name'],
                            'rating' => $row['rating'],
                            'content' => $row['content'],
                            'review_date' => $row['date_created']
                        );
                    }
                    $response = array('success' => true, 'reviews' => $reviews);
                    echo json_encode($response);
                } else {
                    $response = array('success' => false, 'error' => 'Error fetching reviews: ' . $conn->error);
                    echo json_encode($response);
                }
        $conn->close();
    }
?>