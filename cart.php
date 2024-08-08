<?php
require 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Cart Table */
.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px auto;
    background-color: #fff;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.cart-table th, .cart-table td {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
}

.cart-table th {
    background-color: #f4f4f4;
}

.cart-item img {
    width: 100px;
    height: auto;
}

.cart-item input[type="number"] {
    width: 60px;
    padding: 5px;
    margin: 0 auto;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: center;
}

.cart-item button {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    background-color: #dc3545;
    color: white;
    cursor: pointer;
}

.cart-item button:hover {
    background-color: #c82333;
}

form {
    text-align: center;
    margin: 20px 0;
}

button[type="submit"] {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    background-color: #28a745;
    color: white;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #218838;
}

a {
    text-decoration: none;
    color: #007bff;
    margin: 0 10px;
}

a:hover {
    text-decoration: underline;
}

h1 {
    text-align: center;
    color: #333;
    margin-top: 20px;
}

    </style>
</head>
<body>
    <h1>Your Shopping Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
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
                    <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                        <tr class="cart-item">
                            <td><img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                            </td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
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
    <a href="index.php">Continue Shopping</a>
    <a href="checkout.php">Proceed to Checkout</a>

    <script>
        function removeItem(productId) {
            if (confirm("Are you sure you want to remove this item from your cart?")) {
                window.location.href = "removecart.php?product_id=" + productId;
            }
        }
    </script>
</body>
</html>
