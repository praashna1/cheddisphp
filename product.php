<?php
 require 'factory.php';
require 'includes/database.php';

$conn = getDB();
$sql = "SELECT product_id, name, description, price, image, quantity FROM product WHERE factory_id = ?";
$stmt = $conn->prepare($sql);
if (isset($_SESSION['factory_id'])) {
    $factory_id = $_SESSION['factory_id'];
$stmt->bind_param("i", $factory_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    echo "Error: " . $conn->error;
    exit;
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    
.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
   
    gap: 20px; /* Space between product cards */
    margin-left: 240px;
    padding: 20px;
    padding-left: 20px;
    width: calc(100% - 240px); 
    box-sizing: border-box;
}
.product-card {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}



@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr); 
    }
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr); 
    }
}

@media (max-width: 480px) {
    .product-grid {
        grid-template-columns: 1fr; 
    }
}




    </style>
</head>
<body>
<div class="product-grid">
   
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p>Price: Rs.<?php echo number_format($row['price'], 2); ?></p>
                        <p>Available Quantity: <?php echo htmlspecialchars($row['quantity']); ?></p>
                        <form action="edit_product.php" method="get">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form action="delete_product.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <button type="submit" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                        </form>
 

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php

$conn->close();
?>

