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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <nav class="navbar">
                <div class="navbar-logo">
                    <a href="index.php">
                        <img src="img/cheddis1.svg" alt="Logo">
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="userproduct.php">Products</a></li>
                    <li><a href="aboutus.php">About</a></li>
                    <li><a href="user_order.php">My Orders</a></li>
                      <li class="search-dropdown">
                        <a href="#" id="search-btn"><i class="fas fa-search"></i></a>
                        <div class="dropdown-content" id="search-box">
                            <form action="search.php" method="GET">
                                <input type="text" name="query" placeholder="Search...">
                                <button type="submit">Search</button>
                            </form>
                        </div>
                    </li>
                </ul>
                <div class="navbar-icons">
                <?php
                if (isset($_SESSION['username'])) {
                    // User is logged in
                    $username = $_SESSION['username'];
                    echo '<a href="user.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
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
        <script>
      document.addEventListener('DOMContentLoaded', function () {
            const searchBtn = document.getElementById('search-btn');
            const searchBox = document.getElementById('search-box');
            const searchDropdown = document.querySelector('.search-dropdown');

            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchDropdown.classList.toggle('active');
            });

            searchBox.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            window.addEventListener('click', function(e) {
                if (!searchDropdown.contains(e.target)) {
                    searchDropdown.classList.remove('active');
                }
            });
        });
    </script>
</body>
</body>
</html>
