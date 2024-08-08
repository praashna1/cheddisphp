<?php
require 'header.php';


if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission here

    // Validate form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    
    $country = $_POST['country'];
    $payment_method = $_POST['payment_method'];


    require 'includes/database.php';
    $conn = getDB();

    // Insert order details
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, address, city, state, zip, country, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $total_amount = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $_SESSION['cart']));
    $stmt->bind_param('ssssssis', $name, $address, $city, $state, $zip, $country, $payment_method, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }
    // Process payment and save order details
    // This is where you would integrate with a payment gateway

    // Clear the cart after order submission
    unset($_SESSION['cart']);

    // Redirect to a confirmation page or display a success message
    header("Location: confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .checkout-form {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-form h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .checkout-form label {
            display: block;
            margin: 10px 0 5px;
            color: #666;
        }

        .checkout-form input[type="text"], .checkout-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .checkout-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        .checkout-form button:hover {
            background-color: #218838;
        }

        .cart-summary {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-summary th, .cart-summary td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .cart-summary th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                    <tr>
                        <td><img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="100"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item_total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                    <th>$<?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="checkout-form">
        <h2>Billing Information</h2>
        <form action="checkout.php" method="post">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="country">Country</label>
            <input type="text" id="country" name="country" required>

            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
                <option value="credit_card">esewa</option>
                
                <!-- Add more payment options if needed -->
            </select>

            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>
