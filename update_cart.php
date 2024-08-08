<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        }

        header("Location: cart.php");
        exit;
    }
}

header("Location: index.php");
exit;
?>
