
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
        
       
    </div>
</body>
</html>
