<?php

require 'includes/database.php';

session_start();
$conn = getDB();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $desc = $_POST['desc'];
    $quantity=$_POST['quantity'];
   
    $target_dir = "img/";
    $target_file = $target_dir . basename($image);

    if (isset($_SESSION['factory_id'])) {
        $factory_id = $_SESSION['factory_id'];

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO product (name, price, image, description,quantity, factory_id) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssii", $name, $price, $image, $desc, $quantity, $factory_id);
            
        if($stmt->execute()){
            echo "New product uploaded successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            error_log("SQL Error: " . $stmt->error, 3, "errors.log");
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "Factory ID not set. Please log in again.";
}


    
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product</title>
</head>
<body>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
