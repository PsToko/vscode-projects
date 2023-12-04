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
      <!-- Products from the JavaScript file will be inserted here -->
    </tbody>
    <tr>
      <td><input type="text" id="newType" placeholder="Type"></td>
      <td><input type="text" id="newItem" placeholder="Item"></td>
      <td><input type="text" id="newBrand" placeholder="Brand"></td>
      <td><button onclick="addFood()">Add</button></td>
    </tr>
    <button onclick="window.location.href = 'admin.php';">GO BACK</button>
  </table>

  <script src="admin.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      main();
    });
  </script>
</body>
</html>
