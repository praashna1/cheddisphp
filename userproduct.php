<?php
require 'header.php';
require 'includes/database.php';
$conn = getDB();
$sql = "SELECT * FROM product";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px auto;
            max-width: 1200px;
            justify-content: space-around;
        }

        .product-item {
            display: flex;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            width: 250px;
            text-align: center;
            flex-direction: column;
            justify-content: space-between; 
            height: 100%;
        }

        .product-item:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .product-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover; 
            height: 200px;
        }

        .product-name {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .product-price {
            color: #e60000;
            font-size: 1.5em;
            margin: 5px 0;
        }
        .product-actions {
        margin-top: auto; 
        }

        .product-actions button {
            background-color: purple;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .product-actions button:hover {
            background-color: darkorchid;
        }
    </style>
</head>
<body>

<h1 style="text-align: center;">Our Products</h1>

<div class="product-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-item">
        <a href="productinfo.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>">
            <div class="product-image">
                <img src="img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            </div>
            <div class="product-name">
                <?php echo htmlspecialchars($row['name']); ?>
            </div>
            <div class="product-price">
                Rs.<?php echo number_format($row['price'], 2); ?>
            </div>
            <div class="product-actions">
                <button onclick="addToCart(<?php echo $row['product_id']; ?>)">Add to Cart</button>
                <button onclick="window.location.href='product-details.php?id=<?php echo $row['product_id']; ?>'">View Details</button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    function addToCart(productId) {
        window.location.href = 'addcart.php?product_id=' + productId;
    }
</script>

</body>
</html>
