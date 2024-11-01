<?php
require 'header.php';
require 'includes/database.php';
$conn = getDB();

if (isset($_GET['query'])) {
    $search_query = htmlspecialchars($_GET['query']);

    // Use of LIKE for initial broad matching
    $sql = "SELECT * FROM product WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Search Results for "<?php echo $search_query; ?>"</h1>

        <?php
        // Check if any products were found
        if ($result->num_rows > 0) {
            echo '<div class="product-list">';
            
            // Display the products
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product-item">';
                // Ensure correct image path based on your existing structure
                echo '<a href="productinfo.php?product_id=' . htmlspecialchars($row['product_id']) . '">';
                echo '<img src="img/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '</a>';
                echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
                echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                echo '<p>Price: Rs. ' . number_format($row['price'], 2) . '</p>';
                echo '<p>Available Quantity: ' . htmlspecialchars($row['quantity']) . '</p>';
                
                // Add to Cart functionality or Out of Stock message
                // if ($row['quantity'] > 0) {
                //     echo '<form action="addcart.php" method="post">';
                //     echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['product_id']) . '">';
                //     echo '<input type="number" name="quantity" min="1" max="' . htmlspecialchars($row['quantity']) . '" value="1">';
                //     echo '<button type="submit">Add to Cart</button>';
                //     echo '</form>';
                // } else {
                //     echo '<p style="color: red;">Out of Stock</p>';
                // }

                echo '</div>';
            }
            
            echo '</div>';
        } else {
            // No products found
            echo '<p>No products found matching your search query.</p>';
        }
        ?>

    <style>
        .product-list {
            display: flex;
            flex-wrap: wrap;
        }

        .product-item {
            width: 300px;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .product-item img {
            max-width: 100%;
            height: auto;
        }

        .product-item h2 {
            font-size: 18px;
        }

        .product-item p {
            margin: 10px 0;
        }

        .view-product {
            padding: 8px 12px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .view-product:hover {
            background-color: #555;
        }
    </style>
</body>
</html>
