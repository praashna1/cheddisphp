<?php
session_start();
require 'includes/database.php'; // Include your database connection

if (!isset($_SESSION['order_details'])) {
    // Redirect to the homepage if there's no order information
    header("Location: index.php");
    exit;
}

$order_id = $_SESSION['order_id'];
$message = $_SESSION['message'] ?? '';

// Retrieve order details from the database
$conn = getDB();
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Retrieve order items
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing - Order Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Billing Details</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<h2>Order ID: <?php echo htmlspecialchars($order_id); ?></h2>
<p>Total Amount: ₨<?php echo number_format($order['total_amount'], 2); ?></p>

<h3>Order Items</h3>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($item = $order_items->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td>₨<?php echo number_format($item['price'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="index.php">Back to Shopping</a>

</body>
</html>
<?
// Clear session variables
unset($_SESSION['order_id']);
unset($_SESSION['payment_status']);
unset($_SESSION['message']);

?>