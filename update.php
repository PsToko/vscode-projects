<?php
// update.php

// Read the JSON file
$jsonFile = 'items.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// Handle different actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'addFood':
            addFood();
            break;

        case 'deleteFood':
            deleteFood();
            break;

        case 'editFood':
            editFood();
            break;

        default:
            // Handle unsupported actions or return an error
            echo 'Unsupported action.';
            break;
    }

    // Save the updated data back to the JSON file
    $result = file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

    if ($result === false) {
        echo 'Error saving data to items.json.';
    } else {
        echo 'Data saved successfully.';
    }
}

function addFood() {
    global $data;

    $type = $_POST['type'];
    $item = $_POST['item'];
    $brand = $_POST['brand'];

    $data['food'][] = ['type' => $type, 'item' => $item, 'brand' => $brand];
}

function deleteFood() {
    global $data;

    $type = $_POST['type'];
    $item = $_POST['item'];
    $brand = $_POST['brand'];

    foreach ($data['food'] as $key => $food) {
        if ($food['type'] === $type && $food['item'] === $item && $food['brand'] === $brand) {
            unset($data['food'][$key]);
            break;
        }
    }
    // Reindex the array after unset
    $data['food'] = array_values($data['food']);
}

function editFood() {
    global $data;

    $type = $_POST['type'];
    $item = $_POST['item'];
    $brand = $_POST['brand'];
    $newType = $_POST['newType'];
    $newItem = $_POST['newItem'];
    $newBrand = $_POST['newBrand'];

    foreach ($data['food'] as $key => $food) {
        if ($food['type'] === $type && $food['item'] === $item && $food['brand'] === $brand) {
            $data['food'][$key] = ['type' => $newType, 'item' => $newItem, 'brand' => $newBrand];
            break;
        }
    }
}
?>
