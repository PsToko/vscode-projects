<?php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login page or display an error message
    header("Location: demo.php?block=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission and store the announcement
    $selectedItemId = $_POST['selectedItem'];

    // You should save the announcement details to a database or another storage mechanism.
    // For simplicity, we'll store it in a session variable for this example.
    $_SESSION['announcement'] = [
        'itemId' => $selectedItemId,
        'acceptedBy' => null,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="annoucements.css">
    <title>Admin Announcement</title>
</head>
<body>

    <div class="announcement">
        <h1>Admin Announcement</h1>
        <form method="post" action="annoucements.php">
            <p>We are in need of more items. If you have the item listed below, please consider sending it to us:</p>
            <select name="selectedItem" id="itemSelector">
                <!-- The options will be populated dynamically using JavaScript -->
            </select>
            <button type="submit">Send Announcement</button>
        </form>
    </div>

    <script src="annoucements.js"></script>
</body>
</html>
