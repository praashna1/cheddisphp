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
$sql = "SELECT o.order_id, o.customer_name, o.address, o.country, o.payment_method, o.total_amount,
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
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="styles.css">
    <style>
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .order-details th, .order-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .order-details th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
   
    <h2>Your Orders</h2>
    
    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order_id => $order): ?>
            <h3>Order ID: <?php echo htmlspecialchars($order_id); ?></h3>
            <p>Customer Name: <?php echo htmlspecialchars($order['info']['customer_name']); ?></p>
            <p>Address: <?php echo htmlspecialchars($order['info']['address']) . ', ' . htmlspecialchars($order['info']['city']) . ', ' . htmlspecialchars($order['info']['state']) . ' ' . htmlspecialchars($order['info']['zip']) . ', ' . htmlspecialchars($order['info']['country']); ?></p>
            <p>Payment Method: <?php echo htmlspecialchars($order['info']['payment_method']); ?></p>
            <p>Total Amount: $<?php echo number_format($order['info']['total_amount'], 2); ?></p>
            
            <table class="order-details">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['details'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['item_total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
