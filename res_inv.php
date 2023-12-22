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
?>
