<?php require 'factory.php'
?>
 


    <div class="container">
        <h1>Welcome to the Factory Dashboard</h1>
        
     
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
                    
                </tbody>
            </table>
            <a href="dashboard.php" class="btn">Add New Product</a>
        </section>

        

       
    </div>
</body>
</html>
