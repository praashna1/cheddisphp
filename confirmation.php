<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Clear the cart after order confirmation
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .confirmation-container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .confirmation-container h1 {
            color: #333;
        }

        .confirmation-container p {
            color: #666;
            margin: 10px 0;
        }

        .confirmation-container .order-summary {
            width: 100%;
            margin: 20px 0;
        }

        .confirmation-container .order-summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .confirmation-container .order-summary th, .confirmation-container .order-summary td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .confirmation-container .order-summary th {
            background-color: #f4f4f4;
        }

        .confirmation-container a {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            margin-top: 20px;
        }

        .confirmation-container a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been successfully placed. We will process it shortly.</p>
        
        <h2>Order Summary</h2>
        <div class="order-summary">
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
                    if (isset($_SESSION['cart'])):
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
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total</th>
                        <th>$<?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <a href="index.php">Return to Home</a>
    </div>
</body>
</html>
