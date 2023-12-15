// res_items.js

//document.addEventListener("DOMContentLoaded", function() {
    // Check if the user is authorized (you need to implement this logic)
 //   const isAuthorized = checkAuthorization(); // Replace with your logic
  
 //   if (isAuthorized) {
      // If authorized, load the data
  //    loadData();
  //  } else {
      // If not authorized, display a message or redirect
  //    console.error("Unauthorized access. Redirecting...");
      // Redirect or show a message to the user
 //   }
 // });
  
  function loadData() {
    // Fetch data from localStorage
    const resItemsData = localStorage.getItem('resItemsData');
    const resItems = resItemsData ? JSON.parse(resItemsData) : { pharmacies: [] };
  
    // Display data in the table
    let placeholder = document.querySelector("#data-output");
    let out = "";
  
    if (resItems.pharmacies) {
      for (let pharmacy of resItems.pharmacies) {
        out += `
          <tr>
            <td>${pharmacy.type}</td>
            <td>${pharmacy.item}</td>
            <td>${pharmacy.brand}</td>
          </tr>
        `;
      }
    }
  
    placeholder.innerHTML = out;
  }
  
  function transferSelectedItems() {
    // Add your logic here to handle the transfer of items
    // Make sure that the rescuer is authorized to perform this action
    console.log("Transferring selected items...");
  }
  