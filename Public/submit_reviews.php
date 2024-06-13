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
        
        if (!isset($_POST['product_id']) || !isset($_POST['rating']) || !isset($_POST['content']) || !isset($_POST['user_email'])) {
            $response = array('success' => false, 'error' => 'Product ID, rating, content, and user email are required');
            echo json_encode($response);
            exit;
        }

        $product_id = $_POST['product_id'];
        $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
        $user_email = $_POST['user_email']; 
        $rating = $_POST['rating'];
        $content = $_POST['content'];
        $date_created =date('YYYY-MM-DD HH:MM:SS');

        $sql = "INSERT INTO REVIEWS (product_id,parent_id,user_email,rating,content,date_created) VALUES ($product_id,$parent_id,$user_email,$rating,$content,$date_created)";
        
        if($conn->query($sql)===TRUE){
            $response = array('success'=> true);
            echo json_encode($response);
        }
        else{
            $response = array('success'=>false,'error'=> 'Error submitting review: '. $conn->error);
            echo json_encode($response);
        }
        $conn->close();
    }
?>