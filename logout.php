<?php
session_start();

// Clear session cart data
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Clear the cart cookie if it exists
if (isset($_COOKIE['cart'])) {
    
    setcookie('cart', '', time() - 3600, '/');
}

// Unset all session variables and destroy the session
session_unset();
session_destroy();

// Redirect to index or login page after logout
header('Location: index.php');
exit;
?>
