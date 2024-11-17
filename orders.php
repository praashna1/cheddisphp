<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please login to place an order.');
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $query = "INSERT INTO orders (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";

    if (mysqli_query($conn, $query)) {
        echo "Order placed successfully!";
    } else {
        echo "Error placing order: " . mysqli_error($conn);
    }
} else {
    echo "Invalid product or quantity.";
}

?>
