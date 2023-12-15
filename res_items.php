<?php
// admin.php
include 'access.php';

// Start the session
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'rescuer') {
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
  <title>Rescuer Inventory</title>
</head>
<body>
  <table>
    <thead>
      <tr>
        <th>Select</th>
        <th>Type</th>
        <th>Item</th>
        <th>Brand</th>
      </tr>
    </thead>
    <tbody id="data-output">
      <!-- Products from the admin's localStorage will be inserted here -->
    </tbody>
  </table>
  
  <table>
    <thead>
      <tr>
        <th>Select</th>
        <th>Type</th>
        <th>Item</th>
        <th>Brand</th>
      </tr>
    </thead>
    <tbody id="rescuer-data-output">
      <!-- Products from the rescuer's localStorage will be inserted here -->
    </tbody>
  </table>

  <button onclick="transferItems()">Transfer to Rescuer Inventory</button>
  <button onclick="sendItems()">Send to Food Inventory</button>

  <script>
    // Fetch data from the admin's localStorage
    const adminData = localStorage.getItem('foodData');
    const adminInventory = adminData ? JSON.parse(adminData) : { food: [] };

    // Display the admin's inventory
    let adminPlaceholder = document.querySelector("#data-output");
    let adminOut = "";

    if (adminInventory.food) {
      for (let item of adminInventory.food) {
        adminOut += `
          <tr>
            <td><input type="checkbox" class="admin-item-checkbox" data-type="${item.type}" data-item="${item.item}" data-brand="${item.brand}"></td>
            <td>${item.type}</td>
            <td>${item.item}</td>
            <td>${item.brand}</td>
          </tr>
        `;
      }
    }

    adminPlaceholder.innerHTML = adminOut;

    // Fetch data from the rescuer's localStorage
    const rescuerData = localStorage.getItem('resItemsData');
    const rescuerInventory = rescuerData ? JSON.parse(rescuerData) : { pharmacies: [] };

    // Display the rescuer's inventory
    let rescuerPlaceholder = document.querySelector("#rescuer-data-output");
    let rescuerOut = "";

    if (rescuerInventory.pharmacies) {
      for (let item of rescuerInventory.pharmacies) {
        rescuerOut += `
          <tr>
            <td><input type="checkbox" class="rescuer-item-checkbox" data-type="${item.type}" data-item="${item.item}" data-brand="${item.brand}"></td>
            <td>${item.type}</td>
            <td>${item.item}</td>
            <td>${item.brand}</td>
          </tr>
        `;
      }
    }

    rescuerPlaceholder.innerHTML = rescuerOut;

    function transferItems() {
      // Perform the transfer logic
      // For example, you can transfer all selected items from the admin to the rescuer

      // Fetch selected items from admin's inventory
      const selectedAdminItems = document.querySelectorAll('.admin-item-checkbox:checked');
      const selectedAdminData = Array.from(selectedAdminItems).map(item => ({
        type: item.dataset.type,
        item: item.dataset.item,
        brand: item.dataset.brand
      }));

      // Remove transferred items from admin's inventory
      adminInventory.food = adminInventory.food.filter(item =>
        !selectedAdminData.some(selectedItem =>
          selectedItem.type === item.type &&
          selectedItem.item === item.item &&
          selectedItem.brand === item.brand
        )
      );

      // Save the updated admin's inventory back to localStorage
      localStorage.setItem('foodData', JSON.stringify(adminInventory));

      // Transfer selected items from admin to rescuer
      rescuerInventory.pharmacies = rescuerInventory.pharmacies.concat(selectedAdminData);

      // Save the updated rescuer's inventory back to localStorage
      localStorage.setItem('resItemsData', JSON.stringify(rescuerInventory));

      // Refresh the page to reflect changes
      location.reload();
    }

    function sendItems() {
      // Perform the send logic
      // For example, you can send all selected items from the rescuer to the admin

      // Fetch selected items from rescuer's inventory
      const selectedRescuerItems = document.querySelectorAll('.rescuer-item-checkbox:checked');
      const selectedRescuerData = Array.from(selectedRescuerItems).map(item => ({
        type: item.dataset.type,
        item: item.dataset.item,
        brand: item.dataset.brand
      }));

      // Remove sent items from rescuer's inventory
      rescuerInventory.pharmacies = rescuerInventory.pharmacies.filter(item =>
        !selectedRescuerData.some(selectedItem =>
          selectedItem.type === item.type &&
          selectedItem.item === item.item &&
          selectedItem.brand === item.brand
        )
      );

      // Save the updated rescuer's inventory back to localStorage
      localStorage.setItem('resItemsData', JSON.stringify(rescuerInventory));

      // Add sent items to admin's inventory
      adminInventory.food = adminInventory.food.concat(selectedRescuerData);

      // Save the updated admin's inventory back to localStorage
      localStorage.setItem('foodData', JSON.stringify(adminInventory));

      // Refresh the page to reflect changes
      location.reload();
    }
  </script>
</body>
</html>
