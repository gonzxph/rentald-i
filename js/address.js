let selectedLocationType = ''; // To store if it's pickup or drop-off
let selectedAddress = ''; // To store the selected address
let marker = null;  // To store the marker
let cebuProvinceBounds;  // To define the bounds of Cebu Province
var time = '';

document.addEventListener('DOMContentLoaded', function() {
    const pickupModal = document.getElementById('pickupModal');
    const mapModal = new bootstrap.Modal(document.getElementById('mapModal'));

    // Check if the Google object is loaded
    if (typeof google === 'object' && typeof google.maps === 'object') {
        // Define the boundary for Cebu Province (approximate bounds for the whole province)
        cebuProvinceBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(9.5800, 123.0200),  // Southwest corner of Cebu Province
            new google.maps.LatLng(10.8500, 124.1000)   // Northeast corner of Cebu Province
        );

        // Event listener for the pickup input field
        document.getElementById('pickupInput').addEventListener('click', () => {
            selectedLocationType = 'pickup';
            document.getElementById('modalTitle').innerText = 'Choose your pickup location';
        });

        // Event listener for the drop-off input field
        document.getElementById('dropoffInput').addEventListener('click', () => {
            selectedLocationType = 'dropoff';
            document.getElementById('modalTitle').innerText = 'Choose your drop-off location';
        });

        // Initialize Google Places Autocomplete when modal is shown
        pickupModal.addEventListener('shown.bs.modal', function () {
            const input = document.getElementById('autocomplete');
            const autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: 'PH' }, // Restrict to the Philippines
                bounds: cebuProvinceBounds  // Restrict autocomplete results to Cebu Province
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.formatted_address) {
                    selectedAddress = place.formatted_address;
                    document.getElementById("locationResult").innerText = "Address: " + selectedAddress;
                } else {
                    alert("No valid address found. Please try again.");
                }
            });
        });
    } else {
        console.error("Google Maps JavaScript API is not loaded.");
    }
});


    

// For book now button empty
/* document.getElementById('bookingForm').addEventListener('submit', function(event){

    const pickupInput = document.getElementById('pickupInput').value.trim();
    const dropoffInput = document.getElementById('dropoffInput').value.trim();
    const dateTimeInput = document.getElementById('dateTimeInput').value.trim();
    const warningMessage = document.getElementById('warningMessage');

    if(!pickupInput || !dropoffInput || !dateTimeInput){
        event.preventDefault();
        warningMessage.style.display = 'block';

        setTimeout(() => {
            warningMessage.style.display = 'none';
        }, 3000);


    }else{
        alert('success');
    }

}) */


// Function to get the user's current location using Geolocation API
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        document.getElementById("locationResult").innerText = "Geolocation is not supported by this browser.";
    }
}

// Callback for successful geolocation
function showPosition(position) {
    const { latitude, longitude } = position.coords;
    reverseGeocode(latitude, longitude);
}

// Error handling for geolocation
function showError(error) {
    const locationResult = document.getElementById("locationResult");
    switch (error.code) {
        case error.PERMISSION_DENIED:
            locationResult.innerText = "Permission denied for Geolocation.";
            break;
        case error.POSITION_UNAVAILABLE:
            locationResult.innerText = "Location information is unavailable.";
            break;
        case error.TIMEOUT:
            locationResult.innerText = "The request to get user location timed out.";
            break;
        default:
            locationResult.innerText = "An unknown error occurred.";
    }
}

// Function to reverse geocode coordinates to get an address
function reverseGeocode(latitude, longitude) {
    const apiKey = "AIzaSyB70fmdxTT6eYDICyXwGr7rZDy-0DZJSQY"; // Replace with your API key
    const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status === "OK" && data.results.length > 0) {
                selectedAddress = data.results[0].formatted_address;
                document.getElementById("locationResult").innerText = "Address: " + selectedAddress;
            } else {
                document.getElementById("locationResult").innerText = "Unable to retrieve location information.";
            }
        })
        .catch(error => {
            document.getElementById("locationResult").innerText = "Error fetching location: " + error;
        });
}

// Pre-defined address for pickup in garage
function pickupGarage() {
    selectedAddress = "Guada Plains Guadalupe 6000 Cebu City, Philippines";
    document.getElementById("locationResult").innerText = "Address: " + selectedAddress;
}

// Update the selected input field when 'Save changes' is clicked
document.getElementById("saveChangesBtn").addEventListener("click", function() {
    if (selectedAddress) {
        const inputField = selectedLocationType === 'pickup' 
            ? document.getElementById('pickupInput') 
            : document.getElementById('dropoffInput');

        inputField.value = selectedAddress;

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pickupModal'));
        modal.hide();

        selectedAddress = '';
        document.getElementById("locationResult").innerText = '';
    } else {
        document.getElementById("locationResult").innerText = "No location selected.";
    }
});

function openMapModal() {
    const pickupModal = bootstrap.Modal.getInstance(document.getElementById('pickupModal'));
    const mapModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mapModal'));
    pickupModal.hide();
    mapModal.show();
    loadGoogleMap(); 
}

function backToPickupModal() {
    const mapModal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
    const pickupModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pickupModal'));
    mapModal.hide();
    pickupModal.show();
}




function selectMapLocation() {
    const mapModal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
    const inputField = selectedLocationType === 'pickup' 
        ? document.getElementById('pickupInput') 
        : document.getElementById('dropoffInput');

    inputField.value = selectedAddress;
    mapModal.hide();
}


// Load Google Map with clickable functionality
function loadGoogleMap() {
    const mapDiv = document.getElementById('map');
    const map = new google.maps.Map(mapDiv, {
        center: { lat: 10.3157, lng: 123.8854 }, // Centered at Cebu City
        zoom: 10,
        disableDefaultUI: true,
        restriction: {
            latLngBounds: cebuProvinceBounds,
            strictBounds: true
        }
    });

    const geocoder = new google.maps.Geocoder();

    // Click event to place the marker and get the address
    map.addListener('click', function(event) {
        const clickedLocation = event.latLng;

        if (!cebuProvinceBounds.contains(clickedLocation)) {
            alert("Please select a location within Cebu Province.");
            return;
        }

        // Set the marker position and get the address
        setMarkerAndGeocode(geocoder, clickedLocation, map);
    });
}

// Function to place marker and set up the geocoding on drag end
function setMarkerAndGeocode(geocoder, location, map) {
    // If a marker already exists, remove it
    if (marker) {
        marker.setMap(null);
    }

    // Create a new marker
    marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true
    });

    // Geocode the initial location when the marker is placed
    geocodeLatLng(geocoder, location);

    // Add event listener for when the marker is dragged to a new location
    marker.addListener('dragend', function() {
        const newLocation = marker.getPosition();
        geocodeLatLng(geocoder, newLocation);
    });
}

// Function to geocode latitude and longitude to an address
function geocodeLatLng(geocoder, latLng) {
    geocoder.geocode({ location: latLng }, function(results, status) {
        if (status === "OK") {
            if (results[0]) {
                selectedAddress = results[0].formatted_address;
                document.getElementById("locationResultMap").innerText = "Selected Address: " + selectedAddress;
            } else {
                document.getElementById("locationResultMap").innerText = "No address found.";
            }
        } else {
            document.getElementById("locationResultMap").innerText = "Geocoder failed: " + status;
        }
    });
}