<?php require 'header.php'?>
           
            <?php
 $db_host="localhost";
 $db_user="root";
 $db_pass="root";
 $db_name="cheddis";

 $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$sql = "SELECT product.product_id, product.name, product.description, product.price, product.image, product.quantity, factory.name AS factory_name
        FROM product
        JOIN factory ON product.factory_id = factory.factory_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candy Shop</title>
    
    <script>

let currentImageIndex = 0;
        const images = [
            "img/pinky.svg",
            "img/banner.jpg",
            "img/pinky.svg"
        ];

        function changeImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            document.getElementById('banner-image').src = images[currentImageIndex];
            console.log('Image changed to: ', images[currentImageIndex]); 
        }

        setInterval(changeImage, 5000);

        function updateTime() {
            var now = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var dateStr = now.toLocaleDateString('en-US', options);
            var timeStr = now.toLocaleTimeString('en-US');

            document.getElementById('live-banner').innerHTML = 'Welcome! Today is ' + dateStr + ', ' + timeStr;
        }

        setInterval(updateTime, 1000);
    </script>
</head>
<body>
   
    <!-- <div class="products"> -->
       
        <!-- // if ($result->num_rows > 0) {
        //     while ($row = $result->fetch_assoc()) {
        //         echo "<div class='product'>";
        //         echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
        //         echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        //         echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        //         echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
        //         echo "<p>" . htmlspecialchars($row['quantity']) . "</p>";
        //         echo "<p>Factory: " . htmlspecialchars($row['factory_name']) . "</p>";
        //         echo "</div>";
        //     }
        // } else {
        //     echo "No products available.";
        // }
        // $conn->close(); -->
        
        <div id="live-banner" class="live-banner">Welcome! Today is ...</div>
        <div id="picture-banner" class="picture-banner">
        <img id="banner-iamge" src="img\pinky.svg" alt="Special Promotion">
       
    </div>
    </div>
        <div class="container">
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
                            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                            <p>Available Quantity: <?php echo htmlspecialchars($row['quantity']); ?></p>
                            <form action="addcart.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($row['quantity']); ?>" value="1">
                                <button type="submit">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    

                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
    </div>
</body>
</html>

            </div>
        </div>
    </div>
</body>
</html>
