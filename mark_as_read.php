<?php
require 'includes/database.php';
$conn = getDB();

// Check if notification ID is passed via GET
if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Update the notification as read
    $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $notification_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the dashboard after marking as read
    header("Location: layout.php");
} else {
    echo "No notification ID provided.";
}
?>
