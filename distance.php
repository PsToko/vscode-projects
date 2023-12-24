<?php

session_start();

include 'access.php';

function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Earth radius (mean value) in kilometers
    $radius = 6371.0;

    // Haversine formula
    $distance = $radius * $c;

    return $distance;
}

if (isset($_SESSION['user_id'])) {
    $connectedUserId = $_SESSION['user_id'];

    // Assuming you don't have a user_rescuer_mapping table, directly get rescuer details for the user
    $rescuerQuery = "SELECT `res_lat`, `res_lng` FROM `rescuer` WHERE `res_id` = $connectedUserId";
    $rescuerResult = $con->query($rescuerQuery);

    if ($rescuerResult->num_rows > 0) {
        $rescuerRow = $rescuerResult->fetch_assoc();
        $rescuerLat = $rescuerRow["res_lat"];
        $rescuerLng = $rescuerRow["res_lng"];

        // Get all admins
        $adminQuery = "SELECT `adm_lat`, `adm_lng` FROM `admin`";
        $adminResult = $con->query($adminQuery);

        if ($adminResult->num_rows > 0) {
            while ($adminRow = $adminResult->fetch_assoc()) {
                $adminLat = $adminRow["adm_lat"];
                $adminLng = $adminRow["adm_lng"];

                // Calculate distance
                $distance = haversineDistance($rescuerLat, $rescuerLng, $adminLat, $adminLng);

                // Check if the distance is within a certain threshold
                $threshold = 0.1; // Set your desired threshold in kilometers

                if ($distance < $threshold) {
                    // Proceed with your logic, e.g., transfer items
                    echo "Rescuer is close to Admin.";
                } else {
                    echo "Rescuer is not close to Admin.";
                }
            }
        } else {
            echo "No admins found";
        }
    } else {
        echo "Rescuer not found";
    }
} else {
    echo "Connected user ID not provided"; // Handle the case when the user ID is not provided
}

// Close connection
$con->close();

?>
