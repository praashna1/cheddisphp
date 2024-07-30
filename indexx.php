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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome to the Candy Shop!</h1>
    <div class="products">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
                echo "<p>" . htmlspecialchars($row['quantity']) . "</p>";
                echo "<p>Factory: " . htmlspecialchars($row['factory_name']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "No products available.";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
