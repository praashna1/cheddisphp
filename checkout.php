<?php
session_start();

// Retrieve cart from cookie
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

// Debugging: Output cart data


// Ensure that cart data is available
if (empty($cart)) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $payment_method = $_POST['payment_method'];

    require 'includes/database.php';
    $conn = getDB();

    // Insert order details
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, address, country, payment_method, total_amount) VALUES (?, ?, ?, ?, ?)");
    $total_amount = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cart));
    $stmt->bind_param('ssssd', $name, $address, $country, $payment_method, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($cart as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart after order submission by unsetting the cookie
    setcookie('cart', '', time() - 3600, "/"); // Expire the cart cookie

    // Redirect to a confirmation page or display a success message
    header("Location: confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Your CSS here */
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($cart as $product_id => $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                    <tr>
                        <td><img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="100"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item_total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                    <th>$<?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Checkout Form -->
    <form action="checkout.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required><br>

        <label for="country">Country:</label>
        <input type="text" name="country" id="country" required><br>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select><br>

        <button type="submit">Submit Order</button>
    </form>
</body>
</html>
