<!-- products.php -->
<?php
require 'includes/database.php'; // Include your database connection file
$conn=getDB();
$category_id = $_GET['category_id'] ?? null;

if ($category_id) {
    $sql = "SELECT * FROM product WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<h2>Products</h2>';
    while ($product = $result->fetch_assoc()) {
        echo '<div class="product">';
        echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
        echo '<p>' . htmlspecialchars($product['description']) . '</p>';
        echo '<p>Price: $' . htmlspecialchars($product['price']) . '</p>';
        echo '<img src="img/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
        echo '</div>';
    }
} else {
    echo '<p>Please select a category to view products.</p>';
}
?>
