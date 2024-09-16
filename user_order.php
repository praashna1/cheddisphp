<?php
require 'includes/database.php';
require 'header.php';

$conn = getDB();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the list of ordered product IDs
$sql = "SELECT DISTINCT p.product_id, p.name, p.description, p.price, p.image
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.product_id
        WHERE o.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ordered_products = [];

while ($row = $result->fetch_assoc()) {
    $ordered_products[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Ordered Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
        }

        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            width: 200px;
            text-align: center;
        }

        .product-item img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Your Ordered Products</h2>
    
    <?php if (empty($ordered_products)): ?>
        <p>You haven't ordered any products yet.</p>
    <?php else: ?>
        <div class="product-list">
            <?php foreach ($ordered_products as $product): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>$<?php echo number_format($product['price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>

