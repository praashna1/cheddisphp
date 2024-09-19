<?php 
require 'factory.php';
require 'includes/database.php';
$conn = getDB();

// Ensure the user is logged in and is a factory
if (!isset($_SESSION['factory_id'])) {
    header("Location: factlogin.php");
    exit;
}

$factory_id = $_SESSION['factory_id'];

// Fetch orders for products from the factory
$sql = "SELECT o.order_id, o.customer_name, o.address, o.country, o.payment_method, o.total_amount, o.order_status,
               oi.product_id, p.name AS product_name, oi.quantity, oi.price, (oi.quantity * oi.price) AS item_total
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.product_id
        WHERE p.factory_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $factory_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['details'][] = $row;
    $orders[$row['order_id']]['info'] = [
        'customer_name' => $row['customer_name'],
        'address' => $row['address'],
        'country' => $row['country'],
        'payment_method' => $row['payment_method'],
        'total_amount' => $row['total_amount'],
        'status' => $row['order_status'],
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .orders-container {
    display: flex;
    flex-direction: column;
    gap: 20px; /* Adds space between orders */
    margin: 20px;
    margin-left: 220px; /* Adjust this to match the width of your sidebar */
    padding: 20px;
    width: calc(100% - 220px); /* Ensures the order content takes the remaining space */
    box-sizing: border-box;
}

.order-header {
    margin-bottom: 10px;
}

.order-details {
    width: 100%; /* Ensures the table takes full width */
    border-collapse: collapse;
    margin-bottom: 20px;
    
}

.order-details th, 
.order-details td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.order-details th {
    background-color: #f4f4f4;
}
.order-header,
.order-details {
    margin-bottom: 20px;
    display: block;
}


    </style>
</head>
<body>
    <h2>Your Orders</h2>
    <div class="orders-container">
    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order_id => $order): ?>
            <div class="order-header">
                
                <!-- <p>Address: <?php echo htmlspecialchars($order['info']['address']) . ', ' . htmlspecialchars($order['info']['country']); ?></p>
                <p>Payment Method: <?php echo htmlspecialchars($order['info']['payment_method']); ?></p>
                <p>Total Amount: Rs.<?php echo number_format($order['info']['total_amount'], 2); ?></p>
               
                 -->
                <!-- Status Update Form -->
               
            </div>
            
            <table class="order-details">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['details'] as $item): ?>
                        <tr>
                        <td> <?php echo htmlspecialchars($order_id); ?></td>
                        <td> <?php echo htmlspecialchars($order['info']['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>Rs.<?php echo number_format($item['price'], 2); ?></td>
                            <td>Rs.<?php echo number_format($item['item_total'], 2); ?></td>
                            <td>  <form method="post" action="update_status.php" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                    <label for="status">Update Status:</label>
                    <select name="status" id="status">
                        <option value="In Process" <?php echo $order['info']['status'] == 'In Process' ? 'selected' : ''; ?>>In Process</option>
                        <option value="Delivered" <?php echo $order['info']['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="Cancelled" <?php echo $order['info']['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form></td>
                <!-- <?php echo htmlspecialchars($order['info']['status']); ?> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</body>
</html>
