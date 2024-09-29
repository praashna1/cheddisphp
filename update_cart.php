<?php
session_start();
require 'includes/database.php';   // Include the database connection file
$conn = getDB(); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        $error_message = '';

        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);

            // Fetch the available stock for each product from the database
            $query = "SELECT quantity FROM product WHERE product_id = '$product_id'";
            $result = mysqli_query($conn, $query);
            $product = mysqli_fetch_assoc($result);
            $available_quantity = $product['quantity'];

            if ($quantity > $available_quantity) {
                // If requested quantity exceeds available stock, store the error message
                $error_message = "Product ID: $product_id - Requested quantity ($quantity) exceeds available stock ($available_quantity).";
                break; // Stop further processing once an error is found
            }

            if ($quantity <= 0) {
                // Remove item if quantity is zero or less
                unset($cart[$product_id]);
            } elseif (isset($cart[$product_id])) {
                // Update quantity if item exists in cart and stock is sufficient
                $cart[$product_id]['quantity'] = $quantity;
            }
        }

        if (!empty($error_message)) {
            // Set error message in session and redirect back to cart if there's an issue
            $_SESSION['error_message'] = $error_message;
            header("Location: cart.php");
            exit;
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
