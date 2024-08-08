
<?php
require 'factory.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .product-details {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.product-details h1 {
    text-align: center;
    margin-bottom: 20px;
}

.product-image img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    height: auto;
}

.product-info {
    text-align: center;
    margin-top: 20px;
}

.product-info p {
    margin: 10px 0;
}

.product-info form {
    display: inline-block;
    margin-top: 10px;
}

.related-products {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
}

.related-product-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    width: 200px;
    text-align: center;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <!-- Product Upload Form -->
        <h2>Upload Product</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="name">Product Name:</label><br>
            <input type="text" id="name" name="name" required><br>
            <label for="price">Price:</label><br>
            <input type="text" id="price" name="price" required><br>
            <label for="image">Product Image:</label><br>
            <input type="file" id="image" name="image" required><br><br>
            <label for="desc">Description:</label><br>
            <input type="text" id="desc" name="desc" required><br>
            <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>
            <input type="submit" value="Upload Product">
        </form>
        
        <!-- Orders Table -->
        <h2>Orders</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
            </tr>
            <?php
            require 'includes/database.php';
            $conn = getDB();
            $sql = "SELECT orders.id, customers.name as customer_name, products.name as product_name, orders.quantity, orders.total_price, orders.order_date 
                    FROM orders 
                    JOIN customers ON orders.customer_id = customers.id 
                    JOIN products ON orders.product_id = products.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['id']."</td>
                            <td>".$row['customer_name']."</td>
                            <td>".$row['product_name']."</td>
                            <td>".$row['quantity']."</td>
                            <td>".$row['total_price']."</td>
                            <td>".$row['order_date']."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No orders found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
