<?php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'citizen') {
    // Redirect to login page or display an error message
    header("Location: demo.php?block=1");
    exit();
}

// Load the announcement details from the session (or your database)
$announcement = $_SESSION['announcement'] ?? null;

// Check if the announcement has been accepted by the current citizen
$acceptedByCurrentUser = ($announcement && $announcement['acceptedBy'] === session_id());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="annoucements.css">
    <title>Citizen Announcement</title>
</head>
<body>

    <div class="announcement">
        <h1>Citizen Announcement</h1>
        <?php if ($announcement): ?>
            <p>We need more of the selected item. If you can provide it, please press the "Accept" button.</p>
            <p>Selected Item: <?= $announcement['itemId'] ?></p>

            <?php if ($acceptedByCurrentUser): ?>
                <p>You have accepted this announcement.</p>
                <button onclick="cancelAcceptance()">Cancel</button>
            <?php else: ?>
                <button onclick="acceptAnnouncement()">Accept</button>
            <?php endif; ?>

        <?php else: ?>
            <p>No announcement available.</p>
        <?php endif; ?>
    </div>

    <script>
        function acceptAnnouncement() {
            // You should send an AJAX request to the server to record the acceptance
            // For simplicity, we'll just reload the page in this example
            // Also, store the citizen's acceptance in the session for demonstration
            <?php if ($announcement): ?>
                <?php $_SESSION['announcement']['acceptedBy'] = session_id(); ?>
            <?php endif; ?>
            location.reload();
        }

        function cancelAcceptance() {
            // You should send an AJAX request to the server to cancel the acceptance
            // For simplicity, we'll just reload the page in this example
            // Also, remove the citizen's acceptance from the session for demonstration
            <?php if ($announcement): ?>
                <?php $_SESSION['announcement']['acceptedBy'] = null; ?>
            <?php endif; ?>
            location.reload();
        }
    </script>
</body>
</html>