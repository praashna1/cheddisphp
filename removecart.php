<?php
session_start();

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Get cart data from cookie
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

    if (isset($cart[$product_id])) {
        unset($cart[$product_id]);
    }

    // Save updated cart to cookie
    setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days expiration
}

// Redirect to cart page after removal
header("Location: cart.php");
exit;
?>
