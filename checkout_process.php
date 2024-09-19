<?php
require 'includes/database.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to place an order.";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

// Retrieve cart from cookie
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

if (empty($cart)) {
    header("Location: index.php");
    exit;
}

// Calculate total amount from cart items
$total_amount = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart));

// Handle form submission (when user submits billing details)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capture latitude and longitude from the form
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Validate latitude and longitude
    if (empty($latitude) || empty($longitude)) {
        echo "Location not selected. Please select your location on the map.";
        exit;
    }

    // Insert order into the database
    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssddd', $user_id, $_POST['name'], $_POST['address'], $_POST['country'], $_POST['payment_method'], $total_amount, $latitude, $longitude);
    $stmt->execute();
    $order_id = $stmt->insert_id;  // Get the inserted order ID
    $stmt->close();

    // Insert order items
    foreach ($cart as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart
    setcookie('cart', '', time() - 3600, "/");  // Clear cart cookie after submitting order

    // If payment method is eSewa, redirect to eSewa's payment page
    if ($_POST['payment_method'] == 'eSewa') {
        $encoded_total = urlencode($total_amount); // Ensure correct encoding of amount
        $encoded_order_id = urlencode($order_id);
        // Redirect to the eSewa payment gateway with required parameters
        header("Location: esewa_payment.php?total=$total_amount&order_id=$order_id");
        exit;
    } else {
        // If another payment method is used or no payment, redirect to success page
        header("Location: success.php?order_id=$order_id");
        exit;
    }
}
