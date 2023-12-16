function addFood() {
  const newType = document.getElementById('newType').value;
  const newItem = document.getElementById('newItem').value;
  const newBrand = document.getElementById('newBrand').value;

  if (newType && newItem && newBrand) {
    const formData = new FormData();
    formData.append('action', 'addFood');
    formData.append('type', newType);
    formData.append('item', newItem);
    formData.append('brand', newBrand);

    sendRequest(formData);
  } else {
    alert('Please fill in all fields.');
  }
}

function deleteFood(type, item, brand) {
  const formData = new FormData();
  formData.append('action', 'deleteFood');
  formData.append('type', type);
  formData.append('item', item);
  formData.append('brand', brand);

  sendRequest(formData);
}

function editFood(type, item, brand) {
  const newType = prompt('Enter new type:', type);
  const newItem = prompt('Enter new item:', item);
  const newBrand = prompt('Enter new brand:', brand);

  if (newType !== null && newItem !== null && newBrand !== null) {
    const formData = new FormData();
    formData.append('action', 'editFood');
    formData.append('type', type);
    formData.append('item', item);
    formData.append('brand', brand);
    formData.append('newType', newType);
    formData.append('newItem', newItem);
    formData.append('newBrand', newBrand);

    sendRequest(formData);
  }
}

function sendRequest(formData) {
  // Make an AJAX request to handle the form data on the server
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update.php', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Update the HTML content on success
      fetchAndDisplayData();
    }
  };
  xhr.send(formData);
}

function fetchAndDisplayData() {
  // Fetch and display items.json
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'items.json', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var jsonData = JSON.parse(xhr.responseText);
      displayData(jsonData);
    }
  };
  xhr.send();
}

function displayData(data) {
  let placeholder = document.querySelector("#data-output");
  let out = "";

  if (data.food) {
    for (let food of data.food) {
      out += `
        <tr>
          <td>${food.type}</td>
          <td>${food.item}</td>
          <td>${food.brand}</td>
          <td>
            <button onclick="deleteFood('${food.type}', '${food.item}', '${food.brand}')">Delete</button>
            <button onclick="editFood('${food.type}', '${food.item}', '${food.brand}')">Edit</button>
          </td>
        </tr>
      `;
    }
  }

  placeholder.innerHTML = out;
}

function main() {
  fetchAndDisplayData();
}