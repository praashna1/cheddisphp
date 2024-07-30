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
        <!-- <div class="sidebar">
            <div class="sidebar-logo">
                <img src="logo.png" alt="Logo">
            </div>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div> -->
        <div class="main-content">
            <nav class="navbar">
                <div class="navbar-logo">
                    <img src="logo.png" alt="Logo">
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                <div class="navbar-icons">
                <?php
                if (isset($_SESSION['username'])) {
                    // User is logged in
                    $username = $_SESSION['username'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                    echo '<a href="#"><i class="fas fa-shopping-cart"></i>';
                    echo '<a href="logout.php">Logout</a>';
                } else {
                    // User is not logged in
                    echo '<a href="#"><i class="fas fa-shopping-cart"></i>';
                    echo ' <a href="signup.php"><i class="fas fa-user-circle"></i></a>';
                }
                ?>
                </div>
            </nav>
                    
                    
                </ul>
            </nav>
        </div>
</body>
</html>