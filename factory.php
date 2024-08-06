<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <!-- <div class="navbar-logo">
            <img src="logo.png" alt="Factory Logo">
        </div> -->
        <div class="navbar-links">
            <a href="product.php">Dashboard</a>
            <!-- <a href="product.php">Products</a> -->
            <a href="orders.php">Orders</a>
            <a href="profile.php">Profile</a>
            <?php
                if (isset($_SESSION['name'])) {
                    // User is logged in
                    $username = $_SESSION['name'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                   
                    echo '<a href="logout.php">Logout</a>';
                } else {
                    // User is not logged in
                    
                    echo ' <a href="factsign.php"><i class="fas fa-user-circle"></i></a>';
                }
                ?>
        </div>
    </div>