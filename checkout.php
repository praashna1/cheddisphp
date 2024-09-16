<?php
session_start();
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

// Handle form submission (if user submits billing details and selects payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert order into the database
    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssd', $user_id, $_POST['name'], $_POST['address'], $_POST['country'], $_POST['payment_method'], $total_amount);
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Checkout</h1>

<div class="checkout-container">
    <!-- Billing Details Form -->
    <div class="billing-details">
        <h2>Billing Details</h2>
        <form action="checkout.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required>

            <label for="country">Country:</label>
            <input type="text" name="country" id="country" required>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="eSewa">eSewa</option>
                <!-- Add other payment methods here if necessary -->
            </select>

            <button type="submit">Submit Order</button>
        </form>
    </div>

    <!-- Cart Summary -->
    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($cart as $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>₨<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₨<?php echo number_format($item_total, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th>₨<?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>
