<?php
session_start();
require 'includes/database.php'; 


$refId = $_GET['refId'] ?? null; 
$order_id = $_GET['order_id'] ?? null;
$amount = $_GET['amt'] ?? null;

if (!$refId || !$order_id || !$amount) {
    die('Invalid eSewa response.');
}

if (!isset($_SESSION['user_id'])) {
    die('User is not logged in.');
}

$user_id = $_SESSION['user_id'];

$is_payment_successful = true; 

if ($is_payment_successful) {
    $order_details = $_SESSION['order_details'];

    var_dump($order_details);
    $latitude = $order_details['latitude'] ?? null;
    $longitude = $order_details['longitude'] ?? null;
    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssddd', $user_id, $order_details['name'], $order_details['address'], $order_details['country'], $order_details['payment_method'], $amount, $latitude, $longitude);
    $stmt->execute();
    $db_order_id = $stmt->insert_id;
    $stmt->close();

    foreach ($order_details['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $db_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    setcookie('cart', '', time() - 3600, "/");

    $_SESSION['billing_details'] = [
        'order_id' => $db_order_id,
        'name' => $order_details['name'],
        'address' => $order_details['address'],
        'total_amount' => $amount
    ];

    $_SESSION['message'] = 'Order has been placed successfully!';

    unset($_SESSION['order_details']);
    
  
    header("Location: billing.php");
    exit;
} else {
    echo "Transaction Verification Failed.";
    header("Location: checkout.php");
    exit;
}
?>
