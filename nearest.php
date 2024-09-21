<?php
require 'factory.php';
require 'includes/database.php'; // Include your database connection file

// Function to calculate the distance between two points using Haversine formula
function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
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
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view orders.";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch order locations from the database
$conn = getDB();
$stmt = $conn->prepare("SELECT order_id, latitude, longitude FROM orders");
$stmt->execute();
$result = $stmt->get_result();

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

// Get the factory's coordinates (can be hardcoded or fetched from the session/user profile)
$factory_lat = 27.7172; // Example latitude
$factory_lng = 85.3240; // Example longitude

// Calculate distances to all delivery locations
$distances = [];
foreach ($locations as $location) {
    $distance = haversineGreatCircleDistance($factory_lat, $factory_lng, $location['latitude'], $location['longitude']);
    $distances[] = ['order_id' => $location['order_id'], 'distance' => $distance];
}

// Sort locations by distance
usort($distances, function ($a, $b) {
    return $a['distance'] <=> $b['distance'];
});
?>
// Display results
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
    gap: 20px; /* Adds space between orders */
    margin: 20px;
    margin-left: 220px; /* Adjust this to match the width of your sidebar */
    padding: 20px;
    width: calc(100% - 220px); /* Ensures the order content takes the remaining space */
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
    <h2>Nearest Delivery Locations</h2>
    <table>
        <thead>
            <tr>
                <th>Location ID</th>
                <th>Distance (km)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($distances as $location): ?>
                <tr>
                    <td><?php echo htmlspecialchars($location['order_id']); ?></td>
                    <td><?php echo round($location['distance'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>

<?php

$stmt->close();
$conn->close();
?>
