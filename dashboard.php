
<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
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
            <label for="name">Description:</label><br>
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
