<?php
// admin.php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
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
  <title>Food</title>
</head>
<body>
  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Item</th>
        <th>Brand</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="data-output">
      <!-- Products from the PHP file will be inserted here -->
      <?php
      // Read the JSON file and display the items
      $jsonFile = 'items.json';
      $jsonData = file_get_contents($jsonFile);
      $data = json_decode($jsonData, true);

      if (isset($data['food'])) {
          foreach ($data['food'] as $food) {
              echo "
              <tr>
                <td>{$food['type']}</td>
                <td>{$food['item']}</td>
                <td>{$food['brand']}</td>
                <td>
                  <button onclick=\"deleteFood('{$food['type']}', '{$food['item']}', '{$food['brand']}')\">Delete</button>
                  <button onclick=\"editFood('{$food['type']}', '{$food['item']}', '{$food['brand']}')\">Edit</button>
                </td>
              </tr>";
          }
      }
      ?>
    </tbody>
    <tr>
      <td><input type="text" id="newType" placeholder="Type"></td>
      <td><input type="text" id="newItem" placeholder="Item"></td>
      <td><input type="text" id="newBrand" placeholder="Brand"></td>
      <td><button onclick="addFood()">Add</button></td>
    </tr>
    <button onclick="window.location.href = 'admin.php';">GO BACK</button>
  </table>

  <script src="admin.js?v=1"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      main();
    });
  </script>
</body>
</html>
