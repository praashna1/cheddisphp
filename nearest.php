<?php
require 'factory.php';
require 'includes/database.php'; // Include your database connection file

// Function to calculate the distance between two points using Haversine formula
function Distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
    $earthRadius = 6371; // Earth radius in kilometers

    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos($latFrom) * cos($latTo) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance in kilometers
}

// Check if the user is logged in
if (!isset($_SESSION['factory_id'])) {
    $_SESSION['message'] = "Please log in to view orders.";
    header("Location: factlogin.php");
    exit;
}

$factory_id = $_SESSION['factory_id'];

// Fetch order locations from the database
$conn = getDB();
$stmt = $conn->prepare("
    SELECT o.order_id AS order_id, dl.latitude, dl.longitude, oi.product_id
    FROM orders dl
    JOIN orders o ON o.order_id = dl.order_id
    JOIN order_items oi ON oi.order_id = o.order_id
    JOIN product p ON p.product_id = oi.product_id
    WHERE p.factory_id = ?
");
$stmt->bind_param('i', $factory_id);
$stmt->execute();
$result = $stmt->get_result();

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

// Fetch delivered orders for the current factory
$delivered_order_ids = [2, 16]; // Example order IDs for delivered orders; replace with actual IDs or query
$filtered_locations = array_filter($locations, function ($location) use ($delivered_order_ids) {
    return !in_array($location['order_id'], $delivered_order_ids) &&
           !is_null($location['latitude']) && !is_null($location['longitude']);
});

// Get the factory's coordinates
$factory_lat = 27.7172; // Example latitude
$factory_lng = 85.3240; // Example longitude

// Nearest Neighbor Algorithm to calculate the optimized route
function findOptimizedRoute($factory_lat, $factory_lng, $locations) {
    $visited = [];
    $current_lat = $factory_lat;
    $current_lng = $factory_lng;
    $route = [];
    $total_distance = 0;

    // While there are unvisited locations
    while (count($visited) < count($locations)) {
        $nearest_location = null;
        $nearest_distance = PHP_INT_MAX;

        // Find the nearest unvisited location
        foreach ($locations as $index => $location) {
            if (!in_array($index, $visited)) {
                $distance = Distance($current_lat, $current_lng, $location['latitude'], $location['longitude']);
                if ($distance < $nearest_distance) {
                    $nearest_distance = $distance;
                    $nearest_location = $location;
                    $nearest_index = $index;
                }
            }
        }

        // Visit the nearest location
        $route[] = [
            'order_id' => $nearest_location['order_id'],
            'latitude' => $nearest_location['latitude'],
            'longitude' => $nearest_location['longitude'],
            'distance' => $nearest_distance
        ];
        $total_distance += $nearest_distance;

        // Update current position
        $current_lat = $nearest_location['latitude'];
        $current_lng = $nearest_location['longitude'];

        // Mark as visited
        $visited[] = $nearest_index;
    }

    // Return to factory (round trip)
    $return_distance = Distance($current_lat, $current_lng, $factory_lat, $factory_lng);
    $total_distance += $return_distance;

    $route[] = [
        'order_id' => 'Return to Factory',
        'latitude' => $factory_lat,
        'longitude' => $factory_lng,
        'distance' => $return_distance
    ];

    return ['route' => $route, 'total_distance' => $total_distance];
}

$optimized_route = findOptimizedRoute($factory_lat, $factory_lng, $filtered_locations);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Optimized Delivery Route Map</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include Leaflet CSS -->
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .location-container {
            display: flex;
            align-items: center;
            flex-direction: column;
            gap: 20px;
            margin: 20px;
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
            box-sizing: border-box;
        }
        #map {
            height: 500px;
            width: 80%;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="location-container">
        <h2>Optimized Delivery Route Map</h2>
        <div id="map" style="height: 500px;"></div>

<script>
// Initialize the map centered on the factory location
var map = L.map('map').setView([<?php echo $factory_lat; ?>, <?php echo $factory_lng; ?>], 12);

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Factory marker
var factoryMarker = L.marker([<?php echo $factory_lat; ?>, <?php echo $factory_lng; ?>])
    .addTo(map)
    .bindPopup('<b>Factory Location</b>')
    .openPopup();

// Gather optimized route coordinates in the order provided by your PHP array
var waypoints = [
    L.latLng(<?php echo $factory_lat; ?>, <?php echo $factory_lng; ?>), // Start from the factory
    <?php foreach ($optimized_route['route'] as $location): ?>
        L.latLng(<?php echo $location['latitude']; ?>, <?php echo $location['longitude']; ?>),
    <?php endforeach; ?>
    L.latLng(<?php echo $factory_lat; ?>, <?php echo $factory_lng; ?>) // Return to factory
];

// Plot the route using Leaflet Routing Machine
L.Routing.control({
    waypoints: waypoints,
    routeWhileDragging: false,
    createMarker: function(i, wp, nWps) {
        return L.marker(wp.latLng).bindPopup(i === 0 ? 'Factory' : (i === nWps - 1 ? 'Back to Factory' : `Stop ${i}`));
    }
}).addTo(map);
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
