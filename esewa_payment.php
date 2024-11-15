<?php
session_start();


if (!isset($_SESSION['order_details'])) {
    header("Location: checkout.php");
    exit;
}

// Retrieve order details from session
$order_details = $_SESSION['order_details'];
$total_amount = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $order_details['cart']));

$order_id = uniqid(); 

// eSewa Merchant ID and return URLs for success and failure
$esewa_merchant_id = "EPAYTEST";
$success_url = "http://localhost:3000/success.php?order_id={$order_id}&amt={$total_amount}";
$failure_url = "http://localhost:3000/success.php"; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eSewa Payment Redirect</title>
</head>
<body>

<!-- Redirect Form for eSewa -->
<form id= "esewaform" action="https://uat.esewa.com.np/epay/main" method="POST">
    <input type="hidden" name="tAmt" value="<?php echo $total_amount; ?>"> 
    <input type="hidden" name="amt" value="<?php echo $total_amount; ?>"> 
    <input type="hidden" name="txAmt" value="0"> 
    <input type="hidden" name="psc" value="0"> 
    <input type="hidden" name="pdc" value="0"> 
    <input type="hidden" name="scd" value="<?php echo $esewa_merchant_id; ?>"> 
    <input type="hidden" name="pid" value="<?php echo $order_id; ?>"> 
    <input type="hidden" name="su" value="<?php echo $success_url; ?>"> 
    <input type="hidden" name="fu" value="<?php echo $failure_url; ?>"> 

   
</form>
<script>
    window.onload=function(){
        document.getElementById("esewaform").submit();
    };
</script>

</body>
</html>
