<?php
require 'header.php'; 
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $payment_method = $_POST['payment_method'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Store order details in session for later processing
    $_SESSION['order_details'] = [
        'name' => $name,
        'address' => $address,
        'country' => $country,
        'payment_method' => $payment_method,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'cart' => $cart
    ];

    // Calculate total amount for COD (if needed)
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += $item['price'] * $item['quantity']; // Assuming 'price' and 'quantity' are set correctly
    }

    // Before placing the order, check if any product is out of stock
    $conn = getDB();
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

    // After inserting the order details
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, address, country, payment_method, total_amount, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssddd', $user_id, $name, $address, $country, $payment_method, $total_amount, $latitude, $longitude);
    $stmt->execute();
    $db_order_id = $stmt->insert_id;  // Get the last inserted order ID
    $stmt->close();

    // Insert order items
    foreach ($_SESSION['order_details']['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $db_order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart
    setcookie('cart', '', time() - 3600, "/");  // Clear cart cookie after submitting order

    // If payment method is eSewa, redirect to eSewa's payment page
    if ($payment_method == 'eSewa') {
        $encoded_total = urlencode($total_amount);
        $encoded_order_id = uniqid('order_'); // Generate a unique order ID

        // Redirect to the eSewa payment gateway with required parameters
        header("Location: esewa_payment.php?total=$encoded_total&order_id=$encoded_order_id&latitude=$latitude&longitude=$longitude");
        exit;
    } elseif ($payment_method == 'cod') {
        // Redirect to the billing page after COD order is placed
        $_SESSION['billing_details'] = [
            'order_id' => $db_order_id,
            'name' => $name,
            'address' => $address,
            'total_amount' => $total_amount
        ];

        $_SESSION['message'] = 'Order has been placed successfully!';
        
        // Redirect to the billing page
        header("Location: billing.php");
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
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" /> 
 
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  

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
                <option value="cod">Cash on delivery</option>
               
            </select>

 
  <div id="map" style="height: 400px; width: 100%;"></div>
    
  <input type="hidden" id="latitude" name="latitude">
  <input type="hidden" id="longitude" name="longitude">

  
 

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
  const geocoder = L.Control.geocoder({
    defaultMarkGeocode: false
  })
  .on('markgeocode', function(e) {
    const latlng = e.geocode.center;
    map.setView(latlng, 15); // Move map to searched location
    marker.setLatLng(latlng); // Move the marker to the searched location
    updateLatLng(latlng.lat, latlng.lng); // Update the hidden input fields
  })
  .addTo(map);

  // Add search box to the top-left corner
  //L.Control.geocoder().addTo(map);


</script>

</body>
</html>
