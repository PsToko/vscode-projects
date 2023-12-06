let currentMarker = null;

function addMarker(event) {
  const svg = document.getElementById('map');

  // Ask the user if they want to change the marker
  const userResponse = confirm("Do you want to change the marker?");
  
  if (!userResponse) {
    // If the user doesn't want to change, do nothing
    return;
  }

  // Remove the current marker if it exists
  if (currentMarker) {
    svg.removeChild(currentMarker);
  }

  // Get user input for marker coordinates
  const cx = event.clientX - svg.getBoundingClientRect().left;
  const cy = event.clientY - svg.getBoundingClientRect().top;

  // Save the new marker coordinates to localStorage
  localStorage.setItem('markerCoordinates', JSON.stringify({ cx, cy }));

  // Create a new circle element
  const newMarker = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
  newMarker.setAttribute('cx', cx);
  newMarker.setAttribute('cy', cy);
  newMarker.setAttribute('r', '15');
  newMarker.setAttribute('fill', 'blue'); // Customize the fill color

  // Attach an onclick event to the new marker
  newMarker.onclick = function () {
    handleMarkerClick();
  };

  // Set the new marker as the current marker
  currentMarker = newMarker;

  // Append the new marker to the SVG
  svg.appendChild(newMarker);
}

function handleMarkerClick() {
  alert('You clicked on the marker');
  // You can customize this function to perform any desired action
}

// Check if marker coordinates are saved in localStorage
const savedCoordinates = localStorage.getItem('markerCoordinates');

if (savedCoordinates) {
  const { cx, cy } = JSON.parse(savedCoordinates);

  // Create a marker using the saved coordinates
  currentMarker = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
  currentMarker.setAttribute('cx', cx);
  currentMarker.setAttribute('cy', cy);
  currentMarker.setAttribute('r', '15');
  currentMarker.setAttribute('fill', 'blue');

  // Attach an onclick event to the marker
  currentMarker.onclick = function () {
    handleMarkerClick();
  };

  // Append the marker to the SVG
  document.getElementById('map').appendChild(currentMarker);
}
