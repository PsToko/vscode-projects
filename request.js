function populateDropdown() {
    const storedData = localStorage.getItem('foodData');
    const data = storedData ? JSON.parse(storedData) : { food: [] };
    const dropdown = document.getElementById('item');

    if (data.food) {
        for (let food of data.food) {
            const option = document.createElement('option');
            option.value = `${food.type}-${food.item}-${food.brand}`;
            option.text = `${food.type} - ${food.item} - ${food.brand}`;
            dropdown.add(option);
        }
    }
}

function submitRequest() {
    var selectedItem = document.getElementById('item').value;
    var numPeople = document.getElementById('numPeople').value;

    // Extract type, item, and brand from the selected item value
    var [type, item, brand] = selectedItem.split('-');

    // Send an AJAX request to the server to handle the citizen's request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "handle_citizen_request.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Handle the server's response if needed
            console.log(xhr.responseText);
            alert('Request submitted successfully!');
        }
    };
    xhr.send("type=" + type + "&item=" + item + "&brand=" + brand + "&numPeople=" + numPeople);
}

