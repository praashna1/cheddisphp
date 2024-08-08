<?php
session_start();
require 'includes/database.php'; // Include your database connection file

$conn = getDB();

// Dummy data for demonstration. Replace with actual data from your database and session.
$order_id = uniqid(); // Unique order ID
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
} // Total amount to be paid
$return_url = "http://localhost:3000/confirm_payment.php"; // Replace with your actual return URL
$esewa_merchant_id = "EPAYTEST"; // Replace with your eSewa merchant ID

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}

// Insert order into the database
$stmt = $conn->prepare("INSERT INTO orders (order_id, total_amount) VALUES (?, ?)");
$stmt->bind_param("sd", $order_id, $total_amount);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BakeHouse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .order-summary {
            margin-bottom: 20px;
        }
        .order-summary h2 {
            border-bottom: 1px solid #e4e4e4;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .order-summary ul {
            list-style: none;
            padding: 0;
        }
        .order-summary ul li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #e4e4e4;
        }
        .order-summary ul li:last-child {
            border-bottom: none;
        }
        .btn-pay {
            display: block;
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-pay:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <ul>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li>
                        <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                        <span><?php echo $item['price'] * $item['quantity']; ?> NPR</span>
                    </li>
                <?php endforeach; ?>
                <li>
                    <strong>Total</strong>
                    <strong><?php echo $total_amount; ?> NPR</strong>
                </li>
            </ul>
        </div>
        <form action="https://uat.esewa.com.np/epay/main" method="POST">
            <input type="hidden" name="tAmt" value="<?php echo $total_amount; ?>">
            <input type="hidden" name="amt" value="<?php echo $total_amount; ?>">
            <input type="hidden" name="txAmt" value="0">
            <input type="hidden" name="psc" value="0">
            <input type="hidden" name="pdc" value="0">
            <input type="hidden" name="scd" value="<?php echo $esewa_merchant_id; ?>">
            <input type="hidden" name="pid" value="<?php echo $order_id; ?>">
            <input type="hidden" name="su" value="<?php echo $return_url; ?>?q=su&oid=<?php echo $order_id; ?>">
            <input type="hidden" name="fu" value="<?php echo $return_url; ?>?q=fu&oid=<?php echo $order_id; ?>">
            <button type="submit" class="btn-pay">Pay with eSewa</button>
        </form>
    </div>
</body>
</html>