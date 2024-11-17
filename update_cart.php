<?php
session_start();
require 'includes/database.php';   
$conn = getDB(); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        $error_message = '';

        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);
            $query = "SELECT quantity FROM product WHERE product_id = '$product_id'";
            $result = mysqli_query($conn, $query);
            $product = mysqli_fetch_assoc($result);
            $available_quantity = $product['quantity'];

            if ($quantity > $available_quantity) {
                $error_message = "Product ID: $product_id - Requested quantity ($quantity) exceeds available stock ($available_quantity).";
                break; 
            }

            if ($quantity <= 0) {
                unset($cart[$product_id]);
            } elseif (isset($cart[$product_id])) {
                $cart[$product_id]['quantity'] = $quantity;
            }
        }

        if (!empty($error_message)) {
            $_SESSION['error_message'] = $error_message;
            header("Location: cart.php");
            exit;
        }
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); 
        header("Location: cart.php");
        exit;
    }
}
header("Location: index.php");
exit;
?>
