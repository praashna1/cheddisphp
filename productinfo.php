<?php
require 'header.php';
require 'includes/database.php';
$conn = getDB();


$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;


$sql = "SELECT * FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit();
}

$sql_related = "SELECT * FROM product WHERE product_id != ? ORDER BY RAND() LIMIT 4";
$stmt_related = $conn->prepare($sql_related);
$stmt_related->bind_param("i", $product_id);
$stmt_related->execute();
$result_related = $stmt_related->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="product-details">
        
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="product-image">
            <img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p>Price: Rs.<?php echo number_format($product['price'], 2); ?></p>
            <p>Available Quantity: <?php echo htmlspecialchars($product['quantity']); ?></p>
            <?php if ($product['quantity'] > 0): ?>
            <form action="addcart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($product['quantity']); ?>" value="1">
                
                <button type="submit">Add to Cart</button>
            </form>
            <?php else: ?>
        <!-- Show out of stock message if product quantity is 0 -->
        <p style="color: red;">Out of Stock</p>
    <?php endif; ?>
        </div>
    </div>

    <h2>You Might Be Interested In</h2>
    <div class="related-products">
        <?php while ($related = $result_related->fetch_assoc()): ?>
            <div class="related-product-card"> 
                <a href="productinfo.php?product_id=<?php echo $related['product_id']; ?>">
                    <img src="img/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                    <div class="related-product-info">
                        <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                        <p>Price: Rs.<?php echo number_format($related['price'], 2); ?></p>
                        <form action="addcart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $related['product_id']; ?>">
                <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($related['quantity']); ?>" value="1">
                <button type="submit">Add to Cart</button>
            </form>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$stmt_related->close();
$conn->close();
?>
