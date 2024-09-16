<?php
if (isset($_GET['q']) && $_GET['q'] == 'su') {
    $refId = $_GET['refId']; // The transaction reference ID returned by eSewa
    $oid = $_GET['oid']; // The order ID you passed to eSewa
    $amt = $_GET['amt']; // The total amount

    // eSewa verification URL
    $url = "https://uat.esewa.com.np/epay/transrec";

    // Prepare data for transaction verification
    $data = [
        'amt' => $amt,  // Total amount
        'rid' => $refId,  // Transaction reference ID from eSewa
        'pid' => $oid,    // Order ID sent to eSewa
        'scd' => 'EPAYTEST'  // Your eSewa merchant ID
    ];

    // Initialize cURL for the POST request
    $curl = curl_init($url);

    // Configure cURL options
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($curl);
    curl_close($curl);

    // Check response from eSewa (Success or Failure)
    if (strpos($response, 'Success') !== false) {
        // Transaction was successful
        echo "Transaction Successful. Order ID: " . $oid;
        // Proceed with order fulfillment and show success message to the user
    } else {
        // Transaction failed
        echo "Transaction Verification Failed.";
        // Optionally log the error and redirect to a failure page
    }
} else {
    echo "Invalid request.";
}
