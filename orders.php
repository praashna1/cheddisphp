<?php
// Start session to access user_id
session_start();
include('db_connection.php'); // Include your DB connection here

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('Please login to place an order.');
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Get product_id and quantity from POST request (assuming a form is used)
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Insert the order into the database
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
