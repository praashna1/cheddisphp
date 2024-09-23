<?php
require 'header.php';

// Get cart data from cookie
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Include your refined CSS here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        <?php if (!empty($cart)): ?>
            <form action="update_cart.php" method="post">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $product_id => $item): ?>
                            <tr class="cart-item">
                                <td><img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>Rs.<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                </td>
                                <td>Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <button type="button" onclick="removeItem(<?php echo $product_id; ?>)">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Update Cart</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Continue Shopping</a>
            <a href="checkout.php">Proceed to Checkout</a>
        </div>
    </div>

    <script>
        function removeItem(productId) {
            if (confirm("Are you sure you want to remove this item from your cart?")) {
                window.location.href = "removecart.php?product_id=" + productId;
            }
        }
    </script>
</body>
</html>
