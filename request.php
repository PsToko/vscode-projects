<?php
session_start();

// Check if the user is logged in (assuming you have a login mechanism)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'citizen') {
    // Redirect to login page or display an error message
    header("Location: demo.php?block=1");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="items.css">
    <title>Request Item</title>
</head>
<body>
    <h2>Request Item</h2>
    <form id="requestForm">
        <label for="item">Select Item:</label>
        <select id="item" name="item">
            <!-- Options will be dynamically populated by JavaScript -->
        </select>

        <label for="numPeople">Number of People:</label>
        <input type="number" id="numPeople" name="numPeople" required>

        <button type="button" onclick="submitRequest()">Submit Request</button>
    </form>

    <script src="food.js"></script>
    <script src="request.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            main(); // Load items from local storage
            populateDropdown(); // Populate the dropdown with items
        });
    </script>
</body>
</html>
