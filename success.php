<?php
session_start();
require 'includes/database.php'; // Include your database connection file

// Get necessary payment details from eSewa
$refId = $_GET['refId'] ?? null; // Transaction reference ID from eSewa
$order_id = $_GET['order_id'] ?? null;
$amount = $_GET['amt'] ?? null;

if (!$refId || !$order_id || !$amount) {
    die('Invalid eSewa response.');
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User is not logged in.');
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Verify the payment with eSewa (for testing, you may skip this step)
// You would normally call eSewa's verification API here using cURL to confirm the transaction

$is_payment_successful = true; // For testing purposes, we assume it's successful

if ($is_payment_successful) {
    // Retrieve order details from session
    $order_details = $_SESSION['order_details'];

    // Debugging: Check values
var_dump($order_details);
$latitude = $order_details['latitude'] ?? null;
$longitude = $order_details['longitude'] ?? null;

// Debugging: Check latitude and longitude
echo "Latitude: " . $latitude . "<br>";
echo "Longitude: " . $longitude . "<br>";
    // Insert order into the database, including the user_id
    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount,latitude,longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssddd', $user_id, $order_details['name'], $order_details['address'], $order_details['country'], $order_details['payment_method'], $amount,$latitude,$longitude);
    $stmt->execute();
    $db_order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($order_details['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $db_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart and session data
    setcookie('cart', '', time() - 3600, "/");
    unset($_SESSION['order_details']);

    // Set a success message and redirect to the billing page
    $_SESSION['order_id'] = $db_order_id;
    $_SESSION['message'] = 'Order has been placed successfully!';
    header("Location: billing.php");
    exit;
} else {
    // If the payment failed, show a message
    echo "Transaction Verification Failed.";
    header("Location: checkout.php");
    exit;
}
