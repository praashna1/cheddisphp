<?php
if ($_GET['q'] === 'fu') {
    echo "Payment failed!";
    // Handle payment failure, maybe redirect the user to try again
} else {
    echo "Invalid payment response.";
}
?>
