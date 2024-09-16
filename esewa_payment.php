<?php
// Ensure the parameters are passed before trying to use them
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$total_amount = isset($_GET['total']) ? $_GET['total'] : null;

// Check if both parameters are present, otherwise handle the error
if (!$order_id || !$total_amount) {
    die('Missing order ID or total amount.');  // Display an error message and stop execution
}

// eSewa Merchant ID and return URLs for success and failure
$esewa_merchant_id = "EPAYTEST"; // Use your eSewa test merchant ID
$success_url = "http://yourdomain.com/esewa_success.php?q=su&oid=YOUR_ORDER_ID&amt=TOTAL_AMOUNT&refId=TRANSACTION_ID";
$failure_url = "http://yourdomain.com/failure.php?q=fu";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eSewa Payment Redirect</title>
</head>
<body>

<!-- Redirect Form for eSewa -->
<form action="https://uat.esewa.com.np/epay/main" method="POST">
    <input type="hidden" name="tAmt" value="<?php echo $total_amount; ?>"> <!-- Total Amount -->
    <input type="hidden" name="amt" value="<?php echo $total_amount; ?>"> <!-- Actual Amount -->
    <input type="hidden" name="txAmt" value="0"> <!-- Tax Amount -->
    <input type="hidden" name="psc" value="0"> <!-- Service Charge -->
    <input type="hidden" name="pdc" value="0"> <!-- Delivery Charge -->
    <input type="hidden" name="scd" value="<?php echo $esewa_merchant_id; ?>"> <!-- Merchant Code -->
    <input type="hidden" name="pid" value="<?php echo $order_id; ?>"> <!-- Unique Order/Transaction ID -->
    <input type="hidden" name="su" value="<?php echo $success_url; ?>"> <!-- Success URL -->
    <input type="hidden" name="fu" value="<?php echo $failure_url; ?>"> <!-- Failure URL -->

    <button type="submit">Proceed to eSewa</button>
</form>

</body>
</html>
