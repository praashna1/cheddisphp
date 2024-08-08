<?php
session_start();
// Save cart to cookie
if (isset($_SESSION['cart'])) {
    setcookie('cart', json_encode($_SESSION['cart']), time() + (86400 * 30), "/"); // Cookie expires in 30 days
}
session_unset();
session_destroy();
header('Location:index.php');
?>