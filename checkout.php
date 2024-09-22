
<?php require 'header.php'; 

require 'includes/database.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to place an order.";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

// Retrieve cart from cookie
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

if (empty($cart)) {
    header("Location: index.php");
    exit;
}

// Calculate total amount from cart items
$total_amount = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart));

// Handle form submission (if user submits billing details and selects payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    $conn = getDB();
    // Before placing the order, check if any product is out of stock
foreach ($cart as $product_id => $item) {
  $stmt = $conn->prepare("SELECT quantity FROM product WHERE product_id = ?");
  $stmt->bind_param('i', $product_id);
  $stmt->execute();
  $stmt->bind_result($available_quantity);
  $stmt->fetch();
  $stmt->close();

  if ($available_quantity < $item['quantity']) {
      // Product is out of stock, handle error
      echo "Sorry, the product " . $item['name'] . " is out of stock.";
      exit;
  }
}

    // Insert order into the database
    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?,?,?)");
    $stmt->bind_param('issssddd', $user_id, $_POST['name'], $_POST['address'], $_POST['country'], $_POST['payment_method'], $total_amount, $latitude, $longitude);
    $stmt->execute();
    $order_id = $stmt->insert_id;  // Get the inserted order ID
    $stmt->close();

    // Insert order items
    foreach ($cart as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // After inserting the order details
foreach ($cart as $product_id => $item) {
  // Reduce the product quantity based on the ordered quantity
  $stmt = $conn->prepare("UPDATE product SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ?");
  $stmt->bind_param('iii', $item['quantity'], $product_id, $item['quantity']);
  $stmt->execute();

  // Check if product is now out of stock
  $stmt = $conn->prepare("SELECT quantity FROM product WHERE product_id = ?");
  $stmt->bind_param('i', $product_id);
  $stmt->execute();
  $stmt->bind_result($current_quantity);
  $stmt->fetch();
  $stmt->close();

  // Mark the product as "Out of Stock" if quantity reaches zero
  if ($current_quantity <= 0) {
      $stmt = $conn->prepare("UPDATE product SET quantity = 'Out of Stock' WHERE product_id = ?");
      $stmt->bind_param('i', $product_id);
      $stmt->execute();
      $stmt->close();
  }
}

    // Clear the cart
    setcookie('cart', '', time() - 3600, "/");  // Clear cart cookie after submitting order

    // If payment method is eSewa, redirect to eSewa's payment page
    if ($_POST['payment_method'] == 'eSewa') {
        $encoded_total = urlencode($total_amount); // Ensure correct encoding of amount
        $encoded_order_id = urlencode($order_id);
        // Redirect to the eSewa payment gateway with required parameters
        header("Location: esewa_payment.php?total=$total_amount&order_id=$order_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
  
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  
  <!-- Optional: Leaflet Geocoding (for reverse geocoding, if needed) -->
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

</head>
<body>
<h1>Checkout</h1>

<div class="checkout-container">
    <!-- Billing Details Form -->
    <div class="billing-details">
        <h2>Billing Details</h2>
        <form action="checkout.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required>

            <label for="country">Country:</label>
            <input type="text" name="country" id="country" required>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="eSewa">eSewa</option>
                <!-- Add other payment methods here if necessary -->
            </select>

  <!-- Your checkout form -->

  <!-- Map container -->
  <div id="map" style="height: 400px; width: 100%;"></div>
    
  <!-- Hidden inputs to store the latitude and longitude -->
  <input type="hidden" id="latitude" name="latitude">
  <input type="hidden" id="longitude" name="longitude">

  <!-- Your remaining checkout form -->
 

            <button type="submit">Submit Order</button>
        </form>
      
    </div>

    <!-- Cart Summary -->
    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($cart as $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>₨<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₨<?php echo number_format($item_total, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th>₨<?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
  // Initialize the map and set the view to a default location
  var map = L.map('map').setView([27.7172, 85.3240], 15); // Latitude, Longitude, Zoom

  // Load and display tile layer from OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  // Add a draggable marker to the map at the default location
  var marker = L.marker([27.7172, 85.3240], {
    draggable: true
  }).addTo(map);

  // Function to update the latitude and longitude values
  function updateLatLng(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    console.log("Updated Latitude: " + lat + ", Longitude: " + lng);
  }

  // When the user clicks on the map, move the marker and update the coordinates
  map.on('click', function(e) {
    var clickedLat = e.latlng.lat;
    var clickedLng = e.latlng.lng;
    marker.setLatLng([clickedLat, clickedLng]);
    updateLatLng(clickedLat, clickedLng);
  });

  // Optionally, update the coordinates when the user drags the marker
  marker.on('dragend', function(e) {
    var markerLat = marker.getLatLng().lat;
    var markerLng = marker.getLatLng().lng;
    updateLatLng(markerLat, markerLng);
  });

  // Add the geocoder (search box) to the map
  var geocoder = L.Control.geocoder({
    defaultMarkGeocode: false
  })
  .on('markgeocode', function(e) {
    var latlng = e.geocode.center;
    map.setView(latlng, 15); // Move map to searched location
    marker.setLatLng(latlng); // Move the marker to the searched location
    updateLatLng(latlng.lat, latlng.lng); // Update the hidden input fields
  })
  .addTo(map);

  // Add search box to the top-left corner
//   L.Control.geocoder().addTo(map);


</script>

</body>
</html>
