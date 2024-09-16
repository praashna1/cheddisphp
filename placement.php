<?php
// checkout.php

session_start();
require 'includes/database.php';
$conn = getDB();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total_amount = 100.00; // Example total amount
$status = 'In Process'; // Default status

// Start a transaction
$conn->begin_transaction();

try {
    // Insert order
    $sql = "INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ids', $user_id, $total_amount, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the inserted order ID
    $stmt->close();

    // Example order items
    $items = [
        ['product_id' => 1, 'quantity' => 2, 'price' => 25.00],
        // Other items
    ];

    // Insert order items
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($items as $item) {
        $stmt->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();
    echo "Order placed successfully.";
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    echo "Failed to place order: " . $e->getMessage();
}

$conn->close();
?>
