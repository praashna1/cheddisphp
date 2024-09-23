<?php
// Ensure the parameters are passed before trying to use them
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$total_amount = isset($_GET['total']) ? $_GET['total'] : null;
$latitude = isset($_GET['latitude']) ? $_GET['latitude'] : null;
$longitude = isset($_GET['longitude']) ? $_GET['longitude'] : null;

// Check if both parameters are present, otherwise handle the error
if (!$order_id || !$total_amount) {
    die('Missing order ID or total amount.');  // Display an error message and stop execution
}

// eSewa Merchant ID and return URLs for success and failure
$esewa_merchant_id = "EPAYTEST"; // Use your eSewa test merchant ID
// Construct URLs for success and failure
$success_url = "http://localhost:3000/success.php?q=su&oid=$order_id&amt=$total_amount&refId=TRANSACTION_ID"; // Adjust TRANSACTION_ID appropriately
$failure_url = "http://localhost:3000/failure.php?q=fu"; // Or whatever your failure handler is

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
    <input type="hidden" name="tAmt" value="<?php echo $total_amount; ?>">
    <input type="hidden" name="amt" value="<?php echo $total_amount; ?>">
    <input type="hidden" name="txAmt" value="0">
    <input type="hidden" name="psc" value="0">
    <input type="hidden" name="pdc" value="0">
    <input type="hidden" name="scd" value="<?php echo $esewa_merchant_id; ?>">
    <input type="hidden" name="pid" value="<?php echo $order_id; ?>">
    <input type="hidden" name="su" value="<?php echo $success_url; ?>">
    <input type="hidden" name="fu" value="<?php echo $failure_url; ?>">

    <button type="submit">Proceed to eSewa</button>
</form>

</body>
</html>
