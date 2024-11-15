<?php require 'header.php'; ?>

<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "root";
$db_name = "cheddis";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product.product_id, product.name, product.description, product.price, product.image, product.quantity, factory.name AS factory_name
        FROM product
        JOIN factory ON product.factory_id = factory.factory_id";
$result = $conn->query($sql);


// Fetch the top 3 most sold in-stock products
$topProductsSql = "
    SELECT p.product_id, p.name, p.description, p.price, p.image, p.quantity, f.name AS factory_name, SUM(oi.quantity) AS total_sales
    FROM product p
    JOIN factory f ON p.factory_id = f.factory_id
    JOIN order_items oi ON p.product_id = oi.product_id
    WHERE p.quantity > 0  -- Exclude out-of-stock products
    GROUP BY p.product_id
    ORDER BY total_sales DESC
    LIMIT 3";  // Fetch only top 3 products

$topProductsResult = $conn->query($topProductsSql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candy Shop</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file here -->
   
    <script>
        let currentImageIndex = 0;
        const images = [
            "img/pinkbanner.jpg",
            "img/banner.jpg",
            "img/pinkbanner.jpg"
        ];

        function changeImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            document.getElementById('banner-image').src = images[currentImageIndex];
        }

        setInterval(changeImage, 5000);

        function updateTime() {
            var now = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var dateStr = now.toLocaleDateString('en-US', options);
            var timeStr = now.toLocaleTimeString('en-US');

            document.getElementById('live-banner').innerHTML = 'Welcome! Today is ' + dateStr + ', ' + timeStr;
        }

window.onload = function() {
    setInterval(updateTime, 1000);

        setInterval(updateTime, 1000);
        };
    </script>
</head>
<body>
    
    <div id="live-banner" class="live-banner">Welcome! Today is ...</div>
    <div id="picture-banner" class="picture-banner">
        <img id="banner-image" src="img/banner.jpg" alt="Special Promotion">
    </div>
    <div class="container">
    <div class="recommended-section">
    <h2>Recommended Products</h2>
    <div class="recommended-products">
        <?php if ($topProductsResult->num_rows > 0): ?>
            <?php while ($topProduct = $topProductsResult->fetch_assoc()): ?>
                <div class="recommended-product-card">
                    <a href="productinfo.php?product_id=<?php echo htmlspecialchars($topProduct['product_id']); ?>">
                        <img src="img/<?php echo htmlspecialchars($topProduct['image']); ?>" alt="<?php echo htmlspecialchars($topProduct['name']); ?>">
                        <h3><?php echo htmlspecialchars($topProduct['name']); ?></h3>
                        <p>Price: Rs.<?php echo number_format($topProduct['price'], 2); ?></p>
                        <p>Available Quantity: <?php echo htmlspecialchars($topProduct['quantity']); ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No recommended products available.</p>
        <?php endif; ?>
    </div>
</div>

   
        <h1>Our Candies</h1>

        <div class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <a href="productinfo.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>">
                            <img src="img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="product-info">
                                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <p>Price: Rs.<?php echo number_format($row['price'], 2); ?></p>
                                <p>Available Quantity: <?php echo htmlspecialchars($row['quantity']); ?></p>
                                <?php if ($row['quantity'] > 0): ?>
                                <form action="addcart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($row['quantity']); ?>" value="1">
                                    
                                    <button type="submit">Add to Cart</button>
                                </form>
                                <form action="checkout.php" method="get">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                
                                <button type="submit">Buy Now</button>
                            </form>
                                <?php else: ?>
        <!-- Show out of stock message if product quantity is 0 -->
        <p style="color: red;">Out of Stock</p>
    <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
