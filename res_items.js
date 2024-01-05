var rescuerInventory = {};
var adminInventory = {};
var rescuerJsonContent; // Declare rescuerJsonContent at a higher scope

// Function to fetch admin's inventory
function fetchAdminInventory() {
    $.ajax({
        url: 'items.json',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            adminInventory = data.items;
            // Call a function to display the admin's inventory
            displayAdminInventory();
        },
        error: function (status, error) {
            console.error('Failed to fetch admin inventory:', status, error);
        }
    });
}

fetchAdminInventory();

// Function to display admin's inventory
function displayAdminInventory() {
    var adminTableBody = $('#admin-data-output');
    // Clear the existing content
    adminTableBody.html('');

    // Iterate through the admin's inventory and create rows in the table
    $.each(adminInventory, function (itemId, item) {
        var quantity = parseInt(item.details.find(detail => detail.detail_name === 'Quantity').detail_value);

        // Display only items with a quantity greater than 0
        if (quantity > 0) {
            var row = adminTableBody[0].insertRow();

            // Populate the row with item data
            $(row.insertCell(0)).html('<input type="checkbox" class="admin-checkbox" data-item-id="' + itemId + '">');
            $(row.insertCell(1)).text(item.name);
            $(row.insertCell(2)).text(item.category);
            $(row.insertCell(3)).text(quantity);

            // Add the item to rescuer's inventory
            addItemToRescuerInventory(itemId, item);
        }
    });
}

function addItemToRescuerInventory(itemId, item, callback) {
    // Fetch the rescuerId from the server
    $.ajax({
        url: 'get_rescuer_id.php',
        type: 'GET',
        success: function (rescuerId) {
            rescuerId = parseInt(rescuerId);

            // Check if rescuerId is a valid number
            if (!isNaN(rescuerId)) {
                // Append rescuerId to each item in the inventory
                item.rescuerId = rescuerId;

                // Fetch the existing rescuerInventory from local storage
                var existingRescuerInventory = loadRescuerInventoryFromLocal();

                // Check if rescuerInventory.items already exists in the existingRescuerInventory
                if (!existingRescuerInventory.items) {
                    // If not, initialize it as an empty object
                    existingRescuerInventory.items = {};
                }

                // Check if the item already exists in rescuer's inventory
                if (!existingRescuerInventory.items[itemId]) {
                    // If it doesn't exist, add a new item
                    existingRescuerInventory.items[itemId] = {
                        id: item.id,
                        name: item.name,
                        category: item.category,
                        details: item.details,
                        rescuerId: rescuerId
                    };
                } else {
                    // If the item already exists, add the transferred quantity to the existing item
                    var existingItem = existingRescuerInventory.items[itemId];

                    // Find the Quantity detail in the existing item and the transferred item
                    var quantityDetailExisting = existingItem.details.find(detail => detail.detail_name === 'Quantity');
                    var quantityDetailTransferred = item.details.find(detail => detail.detail_name === 'Quantity');

                    // Create a new Quantity detail in the existing item if it doesn't exist
                    if (!quantityDetailExisting) {
                        existingItem.details.push({
                            detail_name: 'Quantity',
                            detail_value: quantityDetailTransferred.detail_value
                        });
                    } else {
                        // Add the transferred quantity to the existing quantity
                        quantityDetailExisting.detail_value = (parseInt(quantityDetailExisting.detail_value) + parseInt(quantityDetailTransferred.detail_value)).toString();
                    }
                }

                // Save the updated rescuer's inventory back to local storage
                localStorage.setItem('rescuerInventory', JSON.stringify(existingRescuerInventory));

                // Call the callback function (if provided)
                if (typeof callback === 'function') {
                    callback();
                }
            } else {
                // Handle the case when rescuerId is not a valid number
                console.error('Invalid rescuer ID:', rescuerId);
            }
        },
        error: function (xhr, status, error) {
            // Handle the case when the rescuer ID is not available or there's an error
            console.error('Failed to fetch rescuer ID:', status, error);
        }
    });
}

function loadRescuerInventoryFromLocal() {
    var storedRescuerInventory = localStorage.getItem('rescuerInventory');
    return storedRescuerInventory ? JSON.parse(storedRescuerInventory) : {};
}

function fetchRescuerInventory() {
    // Dynamically fetch the connected user ID
    var connectedUserId;

    $.ajax({
        url: 'get_rescuer_id.php', // Adjust the URL based on your server setup
        type: 'GET',
        success: function (response) {
            connectedUserId = response;
            console.log(response);

            // AJAX request to distance.php with the dynamically fetched user ID
            $.ajax({
                type: "$_SESSION",
                url: "distance.php",
                data: { connectedUserId: connectedUserId },
                success: function (response) {
                    // Handle the response from PHP
                    console.log(response);

                    // Fetch rescuer's inventory only if the distance calculation is successful
                    if (response === "Rescuer is close to Admin.Rescuer is close to Admin.") {
                        $.ajax({
                            url: 'res_inv.php',
                            type: 'GET',
                            dataType: 'json',
                            success: function (rescuerInventory) {
                                console.log('Parsed Rescuer Inventory:', rescuerInventory);
                                displayRescuerInventory(rescuerInventory);
                            },
                            error: function (status, error) {
                                console.error('Failed to fetch rescuer inventory:', status, error);
                                displayRescuerInventory();  // Display an empty inventory or handle the error as needed
                            }
                        });
                    } else {
                        console.error('Distance is too far from 100 meters');
                        // Handle the case when distance calculation fails
                    }
                },
                error: function (error) {
                    // Handle errors, if any
                    console.error("Error:", error);
                    // Handle the case when there's an error in the AJAX request
                }
            });
        },
        error: function (xhr, status, error) {
            console.error('Failed to fetch connected user ID:', status, error);
        }
    });
}


fetchRescuerInventory();


function displayRescuerInventory(rescuerInventory) {
    console.log('Rescuer Inventory:', rescuerInventory);

    var rescuerTableBody = document.getElementById('rescuer-data-output');
    // Clear the existing content
    rescuerTableBody.innerHTML = '';

    // Check if the 'items' property exists in the rescuerInventory
    if (rescuerInventory && rescuerInventory.items) {
        // Fetch the logged-in rescuer's ID from localStorage
        var loggedInRescuerId = parseInt(localStorage.getItem('rescuerId'));
        console.log(loggedInRescuerId);

        // Iterate through the rescuer's inventory and create rows in the table
        for (var itemId in rescuerInventory.items) {
            if (rescuerInventory.items.hasOwnProperty(itemId)) {
                var item = rescuerInventory.items[itemId];

                // Check if the item belongs to the logged-in rescuer
                if (item.rescuerId === loggedInRescuerId) {
                    var row = rescuerTableBody.insertRow();

                    // Add a checkbox for item selection
                    var checkboxCell = row.insertCell(0);
                    var checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'rescuer-checkbox';
                    checkbox.setAttribute('data-item-id', itemId);
                    checkboxCell.appendChild(checkbox);

                    // Populate the row with item data
                    row.insertCell(1).innerHTML = item.name;
                    row.insertCell(2).innerHTML = item.category;

                    // Assuming 'details' is an array, you might need to adjust this part based on your actual structure
                    var quantityDetail = item.details.find(detail => detail.detail_name === 'Quantity');
                    row.insertCell(3).innerHTML = quantityDetail ? quantityDetail.detail_value : '';
                }
            }
        }
    } else {
        console.error('Invalid format in rescuer inventory:', rescuerInventory);
    }
}

document.getElementById('transferButton').addEventListener('click', function () {
    var selectedItems = document.querySelectorAll('.admin-checkbox:checked');
    var quantityInput = document.getElementById('quantityInput').value;

    console.log('Inside displayAdminInventory', adminInventory);
    console.log('Inside addItemToRescuerInventory', rescuerInventory);

    // Fetch items.json
    fetch('items.json')
        .then(response => response.json())
        .then(itemsData => {
            var transferData = {
                items: {},
            };

            var itemsProcessed = 0;

            selectedItems.forEach(function (checkbox) {
                var itemId = checkbox.getAttribute('data-item-id');
                var adminItem = itemsData.items[itemId];

                // Check if adminItem is defined
                if (adminItem) {
                    // Update quantity in admin's inventory
                    var quantityDetailAdmin = adminItem.details.find(detail => detail.detail_name === 'Quantity');
                    var currentQuantityAdmin = parseInt(quantityDetailAdmin.detail_value);

                    // Check if the current quantity is sufficient for admin
                    if (currentQuantityAdmin >= quantityInput) {
                        // Subtract the chosen quantity for admin
                        var newQuantityForAdmin = currentQuantityAdmin - parseInt(quantityInput);
                        quantityDetailAdmin.detail_value = newQuantityForAdmin.toString();

                        // RescuerItem should be defined here
                        var rescuerItem = {
                            id: adminItem.id,
                            name: adminItem.name,
                            category: adminItem.category,
                            details: adminItem.details.map(detail => {
                                if (detail.detail_name === 'Quantity') {
                                    return {
                                        detail_name: 'Quantity',
                                        detail_value: parseInt(quantityInput),
                                    };
                                } else {
                                    // Copy other details unchanged
                                    return { ...detail };
                                }
                            }),
                            rescuerId: adminItem.rescuerId, // Include rescuerId
                        };

                        // Add the item to transfer data
                        transferData.items[itemId] = {
                            id: rescuerItem.id,
                            name: rescuerItem.name,
                            category: rescuerItem.category,
                            details: rescuerItem.details,
                            rescuerId: rescuerItem.rescuerId,
                            quantityTaken: parseInt(quantityInput),
                        };

                        // Add the item to rescuer's inventory with a callback
                        addItemToRescuerInventory(itemId, rescuerItem, function (error) {
                            itemsProcessed++;

                            if (error) {
                                console.error('Error processing item:', error);
                            }

                            // Create a simplified object for the selected item
                            var simplifiedItem = {
                                id: rescuerItem.id,
                                name: rescuerItem.name,
                                category: rescuerItem.category,
                                details: rescuerItem.details,
                                rescuerId: rescuerItem.rescuerId // Include rescuerId
                            };

                            // Add the simplified item to items
                            transferData.items[itemId] = simplifiedItem;

                            // If all items are processed, save transfer data
                            if (itemsProcessed === selectedItems.length) {
                                // Save the updated content back to items.json
                                saveItemsJson(itemsData);
                                saveTransferData(transferData);
                                console.log('Transfer data saved successfully');
                            } else {
                                console.error('Invalid format');
                            }
                        });
                    } else {
                        console.error('Insufficient quantity for item with ID:', itemId);
                    }
                } else {
                    console.error('Item not found in items.json with id:', itemId);
                }
            });
        })
        .catch(error => console.error('Error loading items.json:', error));
});


function saveItemsJson(content) {
    fetch('update_items.php', { // Replace 'update_items.php' with your server-side script to update items.json
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(content),
    })
        .then(response => response.json())
        .then(data => {
            console.log('Items.json saved successfully:', data);
        })
        .catch(error => console.error('Failed to save items.json:', error));
}

function sendItemsToAdmin() {
    var selectedItems = document.querySelectorAll('.rescuer-checkbox:checked');
    var quantityInput = document.getElementById('sendQuantityInput').value;

    // Fetch rescuer's inventory from localStorage
    var rescuerInventory = loadRescuerInventoryFromLocal();

    var transferData = {
        items: {},
    };

    var itemsProcessed = 0;

    selectedItems.forEach(function (checkbox) {
        var itemId = checkbox.getAttribute('data-item-id');
        var rescuerItem = rescuerInventory.items[itemId];

        // Check if rescuerItem is defined
        if (rescuerItem) {
            // Update quantity in rescuer's inventory
            var quantityDetailRescuer = rescuerItem.details.find(detail => detail.detail_name === 'Quantity');
            var currentQuantityRescuer = parseInt(quantityDetailRescuer.detail_value);

            // Subtract the chosen quantity for rescuer
            var newQuantityForRescuer = currentQuantityRescuer - parseInt(quantityInput);
            quantityDetailRescuer.detail_value = newQuantityForRescuer.toString();

            // AdminItem should be defined here
            var adminItem = {
                id: rescuerItem.id,
                name: rescuerItem.name,
                category: rescuerItem.category,
                details: [...rescuerItem.details], // Copy details array to avoid modifying the original
                adminId: rescuerItem.adminId, // Include adminId
            };

            // Update the item in admin's inventory
            updateAdminInventory(itemId, adminItem, function (error) {
                itemsProcessed++;

                if (error) {
                    console.error('Error updating item in admin inventory:', error);
                }

                // If all items are processed, save transfer data
                if (itemsProcessed === selectedItems.length) {
                    // Add the item to transfer data
                    transferData.items[itemId] = {
                        id: adminItem.id,
                        name: adminItem.name,
                        category: adminItem.category,
                        details: adminItem.details,
                        adminId: adminItem.adminId,
                        quantityReturned: parseInt(quantityInput),
                    };

                    saveTransferData(transferData);
                    console.log('Transfer data saved successfully');
                }
            });
        } else {
            console.error('Item not found in rescuer inventory with id:', itemId);
        }
    });
}

function saveTransferData(transferData) {
    $.ajax({
        url: 'save_transfer_data.php', // Adjust the URL to your server-side script
        type: 'POST',
        contentType: 'application/json;charset=UTF-8',
        data: JSON.stringify(transferData),
        success: function () {
            console.log('Transfer data saved successfully');
        },
        error: function (status, error) {
            console.error('Failed to save transfer data:', status, error);
        }
    });
}


function updateAdminInventory(itemId, item, callback) {
    // Fetch the adminId (user ID) from the server using AJAX
    $.ajax({
        url: 'get_admin_id.php', // Replace with the actual URL to fetch admin ID
        type: 'GET',
        dataType: 'text',
        success: function (adminId) {
            // Convert the response to an integer
            adminId = parseInt(adminId);

            // Check if adminId is a valid number
            if (!isNaN(adminId)) {
                // Append adminId (user ID) to the item
                item.adminId = adminId;

                // Modify the structure of adminItem to match the desired format
                var adminItem = {
                    [itemId]: {
                        id: item.id,
                        name: item.name,
                        category: item.category,
                        details: item.details,
                        adminId: adminId
                    }
                };

                // Merge the item into the admin's inventory
                adminInventory.items = Object.assign({}, adminInventory.items, adminItem);

                // Save the admin's inventory to localStorage (optional)
                localStorage.setItem('adminInventory', JSON.stringify(adminInventory));

                // Call the callback function (if provided)
                if (typeof callback === 'function') {
                    callback();
                }
            } else {
                // Handle the case when adminId is not a valid number
                console.error('Invalid admin ID:', adminId);
            }
        },
        error: function (xhr, status, error) {
            // Handle the case when the user ID is not available or there's an error
            console.error('Failed to fetch admin ID:', status, error);
        }
    });
}


// Handle sending selected items to admin inventory
document.getElementById('sendButton').addEventListener('click', function () {
    var selectedItems = document.querySelectorAll('.rescuer-checkbox:checked');
    var quantityInput = document.getElementById('quantityInput').value;

    var sendItemsData = {
        items: {},
    };

    selectedItems.forEach(function (checkbox) {
        var itemId = checkbox.getAttribute('data-item-id');
        var rescuerItem = rescuerInventory.items[itemId];

        // Check if rescuerItem is defined
        if (rescuerItem) {
            // Update quantity in rescuer's inventory
            var quantityDetailRescuer = rescuerItem.details.find(detail => detail.detail_name === 'Quantity');
            var currentQuantityRescuer = parseInt(quantityDetailRescuer.detail_value);

            // Check if the current quantity is sufficient for rescuer
            if (currentQuantityRescuer >= quantityInput) {
                // Subtract the chosen quantity for rescuer
                var newQuantityForRescuer = currentQuantityRescuer - parseInt(quantityInput);
                quantityDetailRescuer.detail_value = newQuantityForRescuer.toString();

                // AdminItem should be defined here
                var adminItem = {
                    id: rescuerItem.id,
                    name: rescuerItem.name,
                    category: rescuerItem.category,
                    details: [...rescuerItem.details], // Copy details array to avoid modifying the original
                    adminId: rescuerItem.adminId, // Include adminId
                };

                // Add the item to send data
                sendItemsData.items[itemId] = {
                    id: adminItem.id,
                    name: adminItem.name,
                    category: adminItem.category,
                    details: adminItem.details,
                    adminId: adminItem.adminId,
                    quantitySent: parseInt(quantityInput),
                };

                // Update the item in admin's inventory
                updateAdminInventory(itemId, adminItem, function (error) {
                    if (error) {
                        console.error('Error updating item in admin inventory:', error);
                    } else {
                        console.log('Item updated successfully in admin inventory');
                    }
                });
            } else {
                console.error('Insufficient quantity for item with ID:', itemId);
            }
        } else {
            console.error('Item not found in rescuer inventory with id:', itemId);
        }
    });

    // Optionally, you can save send data to the server here
    console.log('Send data:', sendItemsData);
});