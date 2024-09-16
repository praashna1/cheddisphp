<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with Sidebar</title>
    <link rel="stylesheet" href="home.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <nav class="navbar">
                <div class="navbar-logo">
                    <a href="index.php">
                        <img src="img/cheddis.png" alt="Logo">
                    </a>
                </div>

                <!-- Adding Navbar Links -->
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="userproduct.php">Products</a></li>
                    <li><a href="aboutus.php">About</a></li>
                    <li><a href="user_order.php">My Orders</a></li>
                </ul>

                <!-- User and Cart Icons -->
                <div class="navbar-icons">
                <?php
                if (isset($_SESSION['username'])) {
                    // User is logged in
                    $username = $_SESSION['username'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                    echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i></a>';
                    echo '<a href="logout.php">Logout</a>';
                } else {
                    // User is not logged in
                    echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i></a>';
                    echo '<a href="signup.php"><i class="fas fa-user-circle"></i></a>';
                }
                ?>
                </div>
            </nav>
        </div>
</body>
</html>
