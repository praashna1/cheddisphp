<?php
require 'factory.php';   // Ensure this has your session initialization logic
require 'includes/database.php';   // Include the database connection file
$conn = getDB();   // Initialize the connection

// Fetch unread notifications for the factory
$factory_id = $_SESSION['factory_id']; // Ensure the factory ID is available in session
$sql = "SELECT * FROM notifications WHERE factory_id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $factory_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// Function to mark notifications as read (you will implement this in mark_as_read.php)
function markNotificationAsRead($notification_id, $conn) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $notification_id);
    $stmt->execute();
    $stmt->close();
}

// Total Sales (sum of total_amount for orders related to this factory's products)
$sql_sales = "SELECT SUM(o.total_amount) as total_sales 
              FROM orders o 
              JOIN order_items oi ON o.order_id = oi.order_id
              JOIN product p ON oi.product_id = p.product_id
              WHERE p.factory_id = ?";
$stmt_sales = $conn->prepare($sql_sales);
$stmt_sales->bind_param('i', $factory_id);
$stmt_sales->execute();
$result_sales = $stmt_sales->get_result();
$total_sales = $result_sales->fetch_assoc()['total_sales'] ?? 0; // Set default to 0 if no sales
$stmt_sales->close();

// Total Orders (count of distinct orders related to this factory's products)
$sql_orders = "SELECT COUNT(DISTINCT o.order_id) as total_orders
               FROM orders o 
               JOIN order_items oi ON o.order_id = oi.order_id
               JOIN product p ON oi.product_id = p.product_id
               WHERE p.factory_id = ?";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param('i', $factory_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0; // Set default to 0 if no orders
$stmt_orders->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="layout.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h2>Welcome to Your Dashboard</h2>
            <div class="user-info">
            <!-- <?php
                if (isset($_SESSION['name'])) {
                    $username = $_SESSION['name'];
                    echo '<a href="profile.php">Welcome, ' . htmlspecialchars($username) . '!</a>';
                    echo '<a href="logout.php">Logout</a>';
                } else {
                    echo '<a href="factsign.php"><i class="fas fa-user-circle"></i></a>';
                }
            ?> -->
            </div>
        </div>
        
        <div class="summary-cards">
    <div class="card">
        <h3>Total Sales</h3>
        <p>Rs. <?php echo number_format($total_sales, 2); ?></p> <!-- Display total sales -->
    </div>
    <div class="card">
        <h3>Total Orders</h3>
        <p><?php echo $total_orders; ?></p> <!-- Display total orders -->
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
        <script > // Bar chart for Total Sales and Total Orders
var ctx = document.getElementById('salesChart').getContext('2d');
var salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Total Sales', 'Total Orders'], // Labels for the chart
        datasets: [{
            label: 'Factory Overview',
            data: [<?php echo $total_sales; ?>, <?php echo $total_orders; ?>], // Dynamic data from PHP
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)', // Color for Total Sales
                'rgba(153, 102, 255, 0.6)'  // Color for Total Orders
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
 </script>

        <div class="analytics">
            <h3>Top Selling Products</h3>
            <ul>
                <li>Product 1 - Sold 5 units</li>
                <li>Product 2 - Sold 3 units</li>
            </ul>
        </div>

        <div class="notifications">
            <h3>Notifications</h3>
            <?php if (empty($notifications)): ?>
                <p>No new notifications.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($notifications as $notification): ?>
                        <li>
                            <?php echo htmlspecialchars($notification['message']); ?>
                            <a href="mark_as_read.php?id=<?php echo $notification['notification_id']; ?>">Mark as Read</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="dashboard.js"></script>
</body>
</html>
