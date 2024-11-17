<?php
session_start(); 

// Save cart to cookie
setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days expiration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
    } else {
        die("Invalid product or quantity");
    }

    require 'includes/database.php';
    $conn = getDB();
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] += $quantity;
        } else {
            // Adding new product to the cart
            $cart[$product_id] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }

        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); 

        header("Location: cart.php?added=true");
        exit;
    } else {
        echo "Product not found.";
    }
}
?>
