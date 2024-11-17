<?php

require 'includes/database.php';
$conn = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    $deleteOrderItemsSql = "DELETE FROM order_items WHERE product_id = ?";
    $stmt = $conn->prepare($deleteOrderItemsSql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $sql = "DELETE FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: product.php?message=Product deleted successfully");
        exit;
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}
$conn->close();
?>
