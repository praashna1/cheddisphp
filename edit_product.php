<?php
require 'factory.php';
require 'includes/database.php';


$conn = getDB();

$product = null;
$message = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $product_id = $_GET['product_id'] ?? null;
    if ($product_id == null) {
        echo "No product ID.";
        exit;
    }
    $sql = "SELECT product_id, name, description, price, image, quantity FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $message = "Product not found.";
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'] ?: $_POST['current_image'];

    $sql = "UPDATE product SET name = ?, description = ?, price = ?, image = ?, quantity = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsii", $name, $description, $price, $image, $quantity, $product_id);

    if ($stmt->execute()) {
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "img/";
            $target_file = $target_dir . basename($image);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $message = "Product updated successfully.";
            } else {
                $message = "Error uploading image.";
                $messageClass = "error";
            }
        } else {
            $message = "Product updated successfully.";
            $messageClass = "success";
        }
        header("Location: edit_product.php?product_id=" . $product_id . "&message=" . urlencode($message));
        exit;
    } else {
        $message = "Error updating product: " . $conn->error;
    }
}
if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Edit Product</h1>

    <div id="message" class="<?php echo htmlspecialchars($messageClass); ?>">
        <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>

    <?php if ($product): ?>
    <form action="edit_product.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image']); ?>">

        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image">
        <?php if ($product['image']): ?>
            <img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" style="max-width: 100px;">
        <?php endif; ?>

        <button type="submit">Update Product</button>
    </form>
    <?php else: ?>
        <p>Product details could not be retrieved.</p>
    <?php endif; ?>
    </div>
</body>
</html>
