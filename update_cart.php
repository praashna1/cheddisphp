<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity <= 0) {
                // Remove item if quantity is zero or less
                unset($cart[$product_id]);
            } elseif (isset($cart[$product_id])) {
                // Update quantity if item exists in cart
                $cart[$product_id]['quantity'] = $quantity;
            }
        }

        // Save updated cart to cookie
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days expiration

        // Redirect to cart page after update
        header("Location: cart.php");
        exit;
    }
}

// Redirect to index page if not a POST request or quantity is not set
header("Location: index.php");
exit;
?>
