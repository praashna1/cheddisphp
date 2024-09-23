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
$delivered_order_ids = [2, 16];
 
// $delivered_stmt = $conn->prepare("
//     SELECT order_id FROM orders WHERE factory_id = ? AND status = 'Delivered'
// ");
// $delivered_stmt->bind_param('i', $factory_id);
// $delivered_stmt->execute();
// $delivered_result = $delivered_stmt->get_result();

// $delivered_order_ids = [];
// while ($delivered_row = $delivered_result->fetch_assoc()) {
//     $delivered_order_ids[] = $delivered_row['order_id'];
// }

// Filter out delivered orders and invalid coordinates
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
    <link rel="stylesheet" href="styles.css">
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

        table {
            width: 50%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="location-container">
        <h2>Optimized Delivery Route</h2>
        <table>
            <thead>
                <tr>
                    <th>Location ID</th>
                    <th>Distance (km)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($optimized_route['route'] as $location): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($location['order_id']); ?></td>
                        <td><?php echo round($location['distance'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total Distance: </strong><?php echo round($optimized_route['total_distance'], 2); ?> km</p>
    </div>
</body>
</html>

<?php
$stmt->close();
//$delivered_stmt->close();
$conn->close();
?>
