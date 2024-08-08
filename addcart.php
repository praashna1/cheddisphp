<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details from the database
    require 'includes/database.php';
    $conn = getDB();
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Get existing cart from cookie
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

        // If the product is already in the cart, update the quantity
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to the cart array
            $cart[$product_id] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }

        // Save cart to cookie
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days expiration

        // Redirect back to the product page or cart page with a success message
        header("Location: cart.php?added=true");
        exit;
    } else {
        echo "Product not found.";
    }
}
?>