<?php
require 'includes/database.php';
require 'header.php';

$conn = getDB();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Variable to track cancellation success
$cancellation_success = false;

// Handle cancellation request (remove the order and update its status to 'Cancelled')
if (isset($_POST['cancel_order_id'])) {
    $order_id_to_cancel = $_POST['cancel_order_id'];

    // Begin transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // First, delete the order items from the order_items table
        $delete_order_items_sql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($delete_order_items_sql);
        $stmt->bind_param('i', $order_id_to_cancel);
        $stmt->execute();
        $stmt->close();

        // Now update the status of the order to 'Cancelled' in the orders table
        $update_order_status_sql = "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_order_status_sql);
        $stmt->bind_param('ii', $order_id_to_cancel, $user_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Set cancellation success flag
        $cancellation_success = true;
    } catch (Exception $e) {
        // If an error occurs, rollback the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the list of ordered product IDs with their statuses and image paths
$sql = "SELECT DISTINCT p.product_id, p.name, p.description, p.price, p.image, o.order_id, o.order_status
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.product_id
        WHERE o.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ordered_products = [];

while ($row = $result->fetch_assoc()) {
    $ordered_products[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Ordered Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .no-products {
            color: #ff0000;
            font-size: 18px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .cancel-btn {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            background-color: #cc0000;
        }

        .success-message {
            color: green;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>My Order</h2>

    <?php if ($cancellation_success): ?>
        <p class="success-message">Your order has been successfully cancelled.</p>
    <?php endif; ?>

    <?php if (empty($ordered_products)): ?>
        <p class="no-products">You haven't ordered any products yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordered_products as $product): ?>
                    <tr>
                        <td><img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td>Rs.<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['order_status']); ?></td>
                        <td>
                            <?php if ($product['order_status'] !== 'Delivered'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="cancel_order_id" value="<?php echo $product['order_id']; ?>">
                                    <button type="submit" class="cancel-btn">Cancel Order</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
