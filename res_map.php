<?php
// res_map.php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has rescuer privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'rescuer') {
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

    // Update the rescuer's position in the database
    $sqlUpdate = "UPDATE rescuer SET res_lat = ?, res_lng = ? WHERE res_id = ?";
    $stmtUpdate = $con->prepare($sqlUpdate);

    if (!$stmtUpdate) {
        die("Error in preparing statement: " . $con->error);
    }

    $stmtUpdate->bind_param("ddi", $newLat, $newLng, $_SESSION['user_id']);
    $stmtUpdate->execute();

    if ($stmtUpdate->error) {
        // If there is an error during the update, log it
        error_log("Error updating position: " . $stmtUpdate->error);

        // Send a JSON response
        echo json_encode(['success' => false, 'message' => 'Error updating position.']);
        exit();
    }

    // If the update is successful, send a JSON response
    echo json_encode(['success' => true, 'message' => 'Position updated successfully.']);
    exit();
}

// Fetch the admin's position
$sqlAdminMarker = "SELECT * FROM admin";
$resultAdminMarker = $con->query($sqlAdminMarker);

if (!$resultAdminMarker) {
    die("Error in fetching admin marker: " . $con->error);
}

$adminMarker = $resultAdminMarker->fetch_assoc();

// Fetch the rescuer's position
$sqlRescuerMarker = "SELECT * FROM rescuer WHERE res_id = ?";
$stmtRescuerMarker = $con->prepare($sqlRescuerMarker);

if (!$stmtRescuerMarker) {
    die("Error in preparing statement: " . $con->error);
}

$stmtRescuerMarker->bind_param("i", $_SESSION['user_id']);
$stmtRescuerMarker->execute();

if ($stmtRescuerMarker->error) {
    die("Error in executing statement: " . $stmtRescuerMarker->error);
}

$resultRescuerMarker = $stmtRescuerMarker->get_result();

if ($resultRescuerMarker === false) {
    die("Error in getting result: " . $con->error);
}

$rescuerMarker = $resultRescuerMarker->fetch_assoc();

// Close the result sets
$resultAdminMarker->close();
$resultRescuerMarker->close();
$stmtRescuerMarker->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescuer Map</title>
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

        // Display marker for the admin
        L.marker([<?php echo $adminMarker['adm_lat']; ?>, <?php echo $adminMarker['adm_lng']; ?>])
            .addTo(map)
            .bindPopup("Admin's marker.");

        // Display marker for the logged-in rescuer
        var rescuerMarker = L.marker([<?php echo $rescuerMarker['res_lat']; ?>, <?php echo $rescuerMarker['res_lng']; ?>], { draggable: false }).addTo(map);

        var rescuerPopup = L.popup().setContent("This is your marker.");
        rescuerMarker.bindPopup(rescuerPopup);

        // Other code for changing the location and updating the marker position
        var wasDragged = false;

        function changeLocation() {
            if (!wasDragged) {
                // Enable dragging when the button is clicked for the first time
                rescuerMarker.dragging.enable();
                wasDragged = true;
            } else {
                // Disable dragging when the button is clicked again
                rescuerMarker.dragging.disable();
                wasDragged = false;

                if (confirm('Do you want to keep this position?')) {
                    var newLatLng = rescuerMarker.getLatLng();
                    localStorage.setItem('markerPosition', JSON.stringify(newLatLng));

                    // Update the marker position in the same file
                    updateMarkerPosition(newLatLng.lat, newLatLng.lng)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Position updated successfully.');
                            } else {
                                alert('Error updating position. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the position.');
                        });
                } else {
                    // If the user cancels, reset the marker position to the original
                    rescuerMarker.setLatLng(originalPosition);
                }
            }
        }

        function updateMarkerPosition(lat, lng) {
            // Use the same fetch API to update the marker position
            return fetch('res_map.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    update_marker: true,
                    lat: lat,
                    lng: lng,
                }),
            });
        }


        rescuerMarker.on('click', function () {
            rescuerMarker.openPopup();
        });

        document.getElementById('changeLocation').addEventListener('click', changeLocation);


    </script>

</body>
</html>
