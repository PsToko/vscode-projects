document.addEventListener("DOMContentLoaded", function () {
    // Fetch items from items.json (assuming it's in the same directory)
    fetch('items.json')
        .then(response => response.json())
        .then(data => populateSelector(data.items))
        .catch(error => console.error('Error fetching items:', error));
});

function populateSelector(items) {
    const itemSelector = document.getElementById('itemSelector');

    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.text = item.name;
        itemSelector.add(option);
    });
}

function sendItem() {
    const selectedItem = document.getElementById('itemSelector').value;

    // You can perform additional actions here, like sending the selected item to the server
    // For now, let's just log it to the console
    console.log('Sending item with ID:', selectedItem);
}