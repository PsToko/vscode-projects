<?php
// admin_map.php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login page or display an error message
    header("Location: demo.php?block=1");
    exit();
}

// Check if the database connection is successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the request is for updating the marker
if (isset($_POST['update_marker']) && $_POST['update_marker'] == true) {
    // Get the new position
    $newLat = $_POST['lat'];
    $newLng = $_POST['lng'];

    // Update the admin's position in the database
    // Update all admins' positions in the database
    $sqlUpdate = "UPDATE admin SET adm_lat = ?, adm_lng = ?";
    $stmtUpdate = $con->prepare($sqlUpdate);

    if (!$stmtUpdate) {
        die("Error in preparing statement: " . $con->error);
    }

    $stmtUpdate->bind_param("dd", $newLat, $newLng);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Fetch all admins' positions after the update
    $sqlFetchAdmins = "SELECT * FROM admin";
    $resultAdmins = $con->query($sqlFetchAdmins);

    if (!$resultAdmins) {
        die("Error in fetching admins' positions: " . $con->error);
    }

    $admins = [];
    while ($row = $resultAdmins->fetch_assoc()) {
        $admins[] = $row;
    }

    // Close the result set
    $resultAdmins->close();

    // Send a JSON response with updated admins' positions
    echo json_encode(['success' => true, 'message' => 'Admin positions updated successfully.', 'admins' => $admins]);
    exit();

}

// Fetch the admin's position
$sqlAdminMarker = "SELECT * FROM admin WHERE adm_id = ?";
$stmtAdminMarker = $con->prepare($sqlAdminMarker);

if (!$stmtAdminMarker) {
    die("Error in preparing statement: " . $con->error);
}

$stmtAdminMarker->bind_param("i", $_SESSION['user_id']);
$stmtAdminMarker->execute();

if ($stmtAdminMarker->error) {
    die("Error in executing statement: " . $stmtAdminMarker->error);
}

$resultAdminMarker = $stmtAdminMarker->get_result();

if ($resultAdminMarker === false) {
    die("Error in getting result: " . $con->error);
}

$adminMarker = $resultAdminMarker->fetch_assoc();

// Close the result set
$resultAdminMarker->close();
$stmtAdminMarker->close();

// Fetch all rescuers' positions
$sqlRescuers = "SELECT * FROM rescuer";
$resultRescuers = $con->query($sqlRescuers);

if (!$resultRescuers) {
    die("Error in fetching rescuers' markers: " . $con->error);
}

$rescuers = [];
while ($row = $resultRescuers->fetch_assoc()) {
    $rescuers[] = $row;
}

// Close the result set
$resultRescuers->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Map</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body style="margin: 0;">

    <div id="map" style="height: 80vh;"></div>
    <button id="changeLocation">Change Position</button>

    <script>
        var map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Display markers for all rescuers
        var rescuers = <?php echo json_encode($rescuers); ?>;
        var rescuerMarkers = [];

        rescuers.forEach(function (rescuer) {
            var marker = L.marker([rescuer.res_lat, rescuer.res_lng]).addTo(map);
            marker.bindPopup("Rescuer's marker.");
            rescuerMarkers.push(marker);
        });

        // Display marker for the admin
        var adminMarker = L.marker([<?php echo $adminMarker['adm_lat']; ?>, <?php echo $adminMarker['adm_lng']; ?>], {
        draggable: false,
        icon: L.icon({
            iconUrl: 'https://toppng.com/uploads/preview/map-point-google-map-marker-gif-11562858751s4qufnxuml.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            tooltipAnchor: [16, -28],
            shadowSize: [41, 41]
        })
    }).addTo(map);
    adminMarker.bindPopup("Admin's marker.");


        var wasDragged = false;

        function changeLocation() {
            if (!wasDragged) {
                // Enable dragging for the admin marker when the button is clicked for the first time
                adminMarker.dragging.enable();
                wasDragged = true;
            } else {
                // Disable dragging for the admin marker when the button is clicked again
                adminMarker.dragging.disable();
                wasDragged = false;

                if (confirm('Do you want to keep this position?')) {
                    // Update the position of the admin
                    var newLatLng = adminMarker.getLatLng();
                    updateMarkerPosition(adminMarker, newLatLng.lat, newLatLng.lng);
                } else {
                    // If the user cancels, reset the position of the admin to the original
                    adminMarker.setLatLng([adminMarker.options.originalLat, adminMarker.options.originalLng]);
                }
            }
        }

        function updateMarkerPosition(marker, lat, lng) {
            // Use the same fetch API to update the marker position
            fetch('map_greece.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    update_marker: true,
                    lat: lat,
                    lng: lng,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Position updated successfully.');

                    // If update successful, update the positions of all admin markers
                    updateAllAdminMarkers(data.admins);
                } else {
                    alert('Error updating position. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the position.');
            });
        }

        function updateAllAdminMarkers(admins) {
            // Loop through all admin markers and update their positions
            admins.forEach(function (admin) {
                // Skip the current admin (already updated)
                if (admin.adm_id !== <?php echo $_SESSION['user_id']; ?>) {
                    // Find the corresponding admin marker and update its position
                    rescuerMarkers.forEach(function (marker) {
                        if (marker.options.adm_id === admin.adm_id) {
                            marker.setLatLng([admin.adm_lat, admin.adm_lng]);
                        }
                    });
                }
            });
        }

        document.getElementById('changeLocation').addEventListener('click', changeLocation);
    </script>

</body>
</html>
