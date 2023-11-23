fetch("items.json")
  .then(function (response) {
    return response.json();
  })
  .then(function (data) {
    let placeholder = document.querySelector("#data-output");
    let out = "";

    // Display food items
    if (data.food) {
      for (let product of data.food) {
        out += `
          <tr>
            <td>${product.type}</td>
            <td>${product.item}</td>
            <td>${product.brand}</td>
          </tr>
        `;
      }
    }

    placeholder.innerHTML = out;
  })
  .catch(function (error) {
    console.error('Error fetching data:', error);
  });
