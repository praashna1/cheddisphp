<?php
require 'factory.php';   
require 'includes/database.php';
$conn = getDB();  

$factory_id = $_SESSION['factory_id']; 
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

// Function to mark notifications as read is in markread.php
// function markNotificationAsRead($notification_id, $conn) {
//     $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param('i', $notification_id);
//     $stmt->execute();
//     $stmt->close();
// }

$sql_sales = "SELECT SUM(o.total_amount) as total_sales 
              FROM orders o 
              JOIN order_items oi ON o.order_id = oi.order_id
              JOIN product p ON oi.product_id = p.product_id
              WHERE p.factory_id = ?";
$stmt_sales = $conn->prepare($sql_sales);
$stmt_sales->bind_param('i', $factory_id);
$stmt_sales->execute();
$result_sales = $stmt_sales->get_result();
$total_sales = $result_sales->fetch_assoc()['total_sales'] ?? 0; 
$stmt_sales->close();


$sql_orders = "SELECT COUNT(DISTINCT o.order_id) as total_orders
               FROM orders o 
               JOIN order_items oi ON o.order_id = oi.order_id
               JOIN product p ON oi.product_id = p.product_id
               WHERE p.factory_id = ?";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param('i', $factory_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0; 
$stmt_orders->close();


$sql_products = "SELECT p.name AS product_name, SUM(oi.quantity) AS total_ordered
                 FROM order_items oi
                 JOIN product p ON oi.product_id = p.product_id
                 WHERE p.factory_id = ?
                 GROUP BY p.name
                 ORDER BY total_ordered DESC";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->bind_param('i', $factory_id);
$stmt_products->execute();
$result_products = $stmt_products->get_result();
$product_names = [];
$product_order_counts = [];

while ($row = $result_products->fetch_assoc()) {
    $product_names[] = $row['product_name'];
    $product_order_counts[] = $row['total_ordered'];
}
$stmt_products->close();

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
        <p>Rs. <?php echo number_format($total_sales, 2); ?></p> 
    </div>
    <div class="card">
        <h3>Total Orders</h3>
        <p><?php echo $total_orders; ?></p>
    </div>
</div>

<div class="charts-container">
        <div class="charts">
            <div class="chart">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="chart">
                <canvas id="productsPieChart"></canvas>
            </div>
        </div>
        </div>
        <script > 
var ctx = document.getElementById('salesChart').getContext('2d');
var salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Total Sales', 'Total Orders'], 
        datasets: [{
            label: 'Factory Overview',
            data: [<?php echo $total_sales; ?>, <?php echo $total_orders; ?>], 
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
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

var ctxPie = document.getElementById('productsPieChart').getContext('2d');
var productsPieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($product_names); ?>,
        datasets: [{
            data: <?php echo json_encode($product_order_counts); ?>, 
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)', // Colors for the slices of the pie
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top', 
            },
            tooltip: {
                enabled: true 
            }
        }
    }
});

 </script>

        

        <!-- <div class="notifications">
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
        </div> -->
    </div>
    
   
</body>
</html>
