
<?php
require 'factory.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
   
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
         <!-- Display the message -->
         <?php if (!empty($message)): ?>
            <div id="message" class="<?php echo htmlspecialchars($messageClass); ?>">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>
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
