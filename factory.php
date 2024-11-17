<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .custom-icon {
            font-size: 30px;
            color: #FF5733;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <ul>
            <li><a href="layout.php">Dashboard</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="order.php">Orders</a></li>
            <li><a href="upload.php">Add new Product</a></li>
            <li><a href="nearest.php">Location</a></li>
       
            <?php
                if (isset($_SESSION['name'])) {
                  
                    $username = $_SESSION['name'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                   
                    echo '<a href="logout.php">Logout</a>';
                } else {

                    
                    echo ' <a href="factsign.php"><i class="fas fa-user-circle custom-icon></i></a>';
                }
                ?>
                 </ul>
                 </div>
        </div>
    </div>