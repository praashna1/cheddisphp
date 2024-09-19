<?php
session_start();
require 'includes/database.php';
$conn = getDB();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total_amount = 100.00; // Example total amount; replace with actual calculation
$status = 'In Process'; // Default status

// Capture additional form data with default values
$customer_name = isset($_POST['name']) ? $_POST['name'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
echo '<pre>';
print_r($_POST);
echo '</pre>';
// Start a transaction
$conn->begin_transaction();

try {
    // Insert order
    $sql = "INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, order_status, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Adjust 's' for string, 'd' for double, 'i' for integer
    $stmt->bind_param('issssdsss', $user_id, $customer_name, $address, $country, $payment_method, $total_amount, $status, $latitude, $longitude);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the inserted order ID
    $stmt->close();

    // Retrieve cart from cookie (if applicable)
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

    // Insert order items
    if (!empty($cart)) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($cart as $product_id => $item) {
            $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Clear the cart
    setcookie('cart', '', time() - 3600, "/");  // Clear cart cookie

    // Commit transaction
    $conn->commit();
    echo "Order placed successfully.";

    // Redirect based on payment method
    if ($payment_method === 'eSewa') {
        header("Location: esewa_payment.php?total=$total_amount&order_id=$order_id");
        exit;
    } else {
        header("Location: success.php?order_id=$order_id");
        exit;
    }
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    echo "Failed to place order: " . $e->getMessage();
}

$conn->close();
?>
