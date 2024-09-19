<?php require 'factory.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="layout.css">
    <!-- Add your Chart.js script here -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
   
    
    <div class="main-content">
        <div class="header">
            <h2>Welcome to Your Dashboard</h2>
            <div class="user-info">
            <?php
                if (isset($_SESSION['name'])) {
                    // User is logged in
                    $username = $_SESSION['name'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                   
                    echo '<a href="logout.php">Logout</a>';
                } else {
                    // User is not logged in
                    
                    echo ' <a href="factsign.php"><i class="fas fa-user-circle"  ></i></a>';
                }
                ?>
            </div>
        </div>
        
        <div class="summary-cards">
            <div class="card">
                <h3>Total Sales</h3>
                <p>Rs. 0.00</p>
            </div>
            <div class="card">
                <h3>Total Orders</h3>
                <p>0</p>
            </div>
        </div>
        
        <div class="charts">
            <div class="chart">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="chart">
                <canvas id="productsChart"></canvas>
            </div>
        </div>

        <div class="analytics">
            <h3>Top Selling Products</h3>
            <ul>
                <li>Product 1 - Sold 5 units</li>
                <li>Product 2 - Sold 3 units</li>
            </ul>
        </div>

        <div class="notifications">
            <h3>Recent Notifications</h3>
            <p>You have received an order.</p>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
