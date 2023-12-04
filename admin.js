
function addFood() {
  const newType = document.getElementById('newType').value;
  const newItem = document.getElementById('newItem').value;
  const newBrand = document.getElementById('newBrand').value;

  if (newType && newItem && newBrand) {
    const storedData = localStorage.getItem('foodData');
    const data = storedData ? JSON.parse(storedData) : { food: [] };

    data.food.push({ type: newType, item: newItem, brand: newBrand });

    localStorage.setItem('foodData', JSON.stringify(data));
    refreshData(data);
  } else {
    alert('Please fill in all fields.');
  }
}

function deleteFood(type, item, brand) {
  const storedData = localStorage.getItem('foodData');
  const data = storedData ? JSON.parse(storedData) : { food: [] };

  const indexToDelete = data.food.findIndex(food => (
    food.type === type &&
    food.item === item &&
    food.brand === brand
  ));

  if (indexToDelete !== -1) {
    data.food.splice(indexToDelete, 1);
    localStorage.setItem('foodData', JSON.stringify(data));
    refreshData(data);
  }
}

function editFood(type, item, brand) {
  const newType = prompt('Enter new type:', type);
  const newItem = prompt('Enter new item:', item);
  const newBrand = prompt('Enter new brand:', brand);

  if (newType !== null && newItem !== null && newBrand !== null) {
    const storedData = localStorage.getItem('foodData');
    const data = storedData ? JSON.parse(storedData) : { food: [] };

    const indexToEdit = data.food.findIndex(food => (
      food.type === type &&
      food.item === item &&
      food.brand === brand
    ));

    if (indexToEdit !== -1) {
      data.food[indexToEdit] = { type: newType, item: newItem, brand: newBrand };
      localStorage.setItem('foodData', JSON.stringify(data));
      refreshData(data);
    }
  }
}

function main() {
  const storedData = localStorage.getItem('foodData');
  const data = storedData ? JSON.parse(storedData) : { food: [] };

  refreshData(data);
}

function refreshData(data) {
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
