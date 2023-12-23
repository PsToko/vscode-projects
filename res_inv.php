<?php

session_start();

include 'resid_finder.php';

$rescuerId = getLoggedInRescuerId();

if ($rescuerId !== null) {
    // Load content from rescuer.json
    $jsonContent = json_decode(file_get_contents('rescuer.json'), true);

    // Check if $jsonContent is not null before using it
    if ($jsonContent !== null && isset($jsonContent['items'])) {
        // Filter items based on rescuer's ID
        $filteredItems = array_filter($jsonContent['items'], function ($item) use ($rescuerId) {
            return isset($item['rescuerId']) && $item['rescuerId'] == $rescuerId;
        });

        // Create a new JSON structure with filtered items
        $filteredContent = ['items' => $filteredItems];

        // Echo the filtered content as JSON
        header('Content-Type: application/json');
        echo json_encode($filteredContent);
    } else {
        // Handle the case when $jsonContent is null or does not have the expected structure
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid JSON content']);
    }
} else {
    // Handle the case when the user is not logged in
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in']);
}

// Check if the request method is POST (for updating the rescuer's JSON content)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the JSON data from the request body
    $jsonContent = file_get_contents('php://input');
    
    // Decode the JSON data into an associative array
    $data = json_decode($jsonContent, true);

    // Check if the decoding was successful
    if ($data !== null) {
        // Update the rescuer.json file with the new data
        updateRescuerJson($data);
    } else {
        // Send an error response for invalid JSON data
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
    }
}

// Function to update rescuer.json
function updateRescuerJson($data) {
    $filePath = 'rescuer.json';

    // Encode the new data as JSON
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT);

    // Save the updated content back to rescuer.json
    file_put_contents($filePath, $jsonContent);

    // Send a response
    echo json_encode(['success' => true]);
}

?>
