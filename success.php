<?php
session_start();
require 'includes/database.php'; // Include your database connection file
error_log("Payment Params: " . json_encode($data));

if (isset($_GET['q']) && $_GET['q'] == 'su') {
    $refId = $_GET['refId']; // The transaction reference ID returned by eSewa
    $oid = $_GET['oid']; // The order ID you passed to eSewa
    $amt = $_GET['amt']; // The total amount

    // eSewa verification URL
    $url = "https://uat.esewa.com.np/epay/transrec";

    // Prepare data for transaction verification
    $data = [
        'amt' => $amt,  
        'rid' => $refId,  
        'pid' => $oid,    
        'scd' => 'EPAYTEST'  
    ];

    // Initialize cURL for the POST request
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl); // Ensure this is executed

    if ($response === false) {
        // Handle cURL error
        die('cURL error: ' . curl_error($curl));
    }

    curl_close($curl); // Close the cURL session

    // Check response from eSewa (Success or Failure)
    if (strpos($response, 'Success') !== false) {
        // Transaction was successful
        $_SESSION['order_id'] = $oid;  // Store the order ID in session
        $_SESSION['payment_status'] = 'success';  // Payment status
        $_SESSION['message'] = 'Order has been placed successfully!';  // Success message

        // Insert the order into the database
        $conn = getDB();
        $user_id = $_SESSION['user_id']; // Get the user ID from the session

        // Retrieve order details from session or wherever you store them
        // For example, you might have the customer details in session
        $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?,?,?)");
    $stmt->bind_param('issssddd', $user_id, $_POST['name'], $_POST['address'], $_POST['country'], $_POST['payment_method'], $total_amount, $latitude, $longitude);
    $stmt->execute();

        // Redirect to the billing page
        header("Location: billing.php");
        exit;
    } else {
        // Transaction failed
        echo "Transaction Verification Failed.";
    }
} else {
    echo "Invalid request.";
}
?>
