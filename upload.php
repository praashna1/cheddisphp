<?php

require 'factory.php';

require 'includes/database.php';
$conn = getDB();

$message = ""; // Initialize message variable
$messageClass = ""; // Initialize message class variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $desc = $_POST['desc'];
    $quantity = $_POST['quantity'];

    $target_dir = "img/";
    $target_file = $target_dir . basename($image);

    if (isset($_SESSION['factory_id'])) {
        $factory_id = $_SESSION['factory_id'];

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO product (name, price, image, description, quantity, factory_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssii", $name, $price, $image, $desc, $quantity, $factory_id);
            
            if ($stmt->execute()) {
                // Set success message
                $message = "New product uploaded successfully.";
                $messageClass = "success";
            } else {
                // Set error message
                $message = "Error: " . $sql . "<br>" . $conn->error;
                $messageClass = "error";
                error_log("SQL Error: " . $stmt->error, 3, "errors.log");
            }
        } else {
            // Set error message for file upload
            $message = "Sorry, there was an error uploading your file.";
            $messageClass = "error";
        }
    } else {
        // Set error message for session not set
        $message = "Factory ID not set. Please log in again.";
        $messageClass = "error";
    }
}

// Check for message in session
if (isset($_SESSION['upload_message'])) {
    $message = $_SESSION['upload_message'];
    $messageClass = $_SESSION['upload_message_class'];
    unset($_SESSION['upload_message']); // Clear the message after displaying
    unset($_SESSION['upload_message_class']); // Clear the message class after displaying
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Upload Product</h1>

        <!-- Display the message -->
        <?php if (!empty($message)): ?>
            <div id="message" class="<?php echo htmlspecialchars($messageClass); ?>">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Product Upload Form -->
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
