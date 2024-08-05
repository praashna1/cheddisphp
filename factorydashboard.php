

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="logo.png" alt="Factory Logo">
        </div>
        <div class="navbar-links">
            <a href="factorydashboard.php">Dashboard</a>
            <a href="edit_product.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Welcome to the Factory Dashboard</h1>
        
        <!-- Example Section: Products -->
        <section id="products">
            <h2>Your Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>image</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
            require 'includes/database.php';
            $conn = getDB();
            $sql = "SELECT product_id, name, description, price, image, quantity FROM product WHERE product_id = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['product_id']."</td>
                            <td>".$row['image']."</td>
                            <td>".$row['name']."</td>
                            <td>".$row['quantity']."</td>
                            <td>".$row['price']."</td>
                            <td>".$row['description']."</td>
                          </tr>";
                }
            } ?>
             
              
                        <td>
                            <a href="edit_product.php?id=1"><i class="fas fa-edit"></i></a>
                            <a href="delete_product.php?id=1"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <!-- Additional rows will be populated from the database -->
                </tbody>
            </table>
            <a href="dashboard.php" class="btn">Add New Product</a>
        </section>

        

        <!-- Example Section: Orders -->
        <section id="orders">
            <h2>Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example row -->
                    <tr>
                        <td>#1234</td>
                        <td>Example Candy</td>
                        <td>John Doe</td>
                        <td>2</td>
                        <td>$10.00</td>
                        <td>Shipped</td>
                    </tr>
                    <!-- Additional rows will be populated from the database -->
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
