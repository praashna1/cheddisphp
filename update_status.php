<?php 
require 'includes/database.php';
$conn = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $order_id);

    if ($stmt->execute()) {
        header("Location: order.php"); // Redirect to the orders page
        exit;
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
}
$conn->close();
?>
<form method="POST">
    <label for="order_status">Update Order Status:</label>
    <select name="order_status" id="order_status">
        <option value="Cancelled">Cancelled</option>
        <option value="In Process">In Process</option>
        <option value="Delivered">Delivered</option>
    </select>
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <button type="submit">Update Status</button>
</form>
