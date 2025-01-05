<?php
session_start();
include 'admin_header/admin_header.php';
include 'admin_header/admin_nav.php';  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>D&I CEBU CAR RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="car_list.css">
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="outer-box" style="margin-top:15px;">
            <h1 class="text-left mb-3">List Of Vehicles</h1>
            
            <!-- Display Success or Error Messages -->
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                    $_SESSION['success'] . 
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . 
                    $_SESSION['error'] . 
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                unset($_SESSION['error']);
            }
            ?>

            <!-- Search Bar -->
            <div class="search-container mb-4">
                <input class="form-control me-2" type="search" id="searchInput" placeholder="Search by Model or Brand" aria-label="Search">
            </div>

            <!-- Car Availability Filter Dropdown -->
            <div class="availability-container mb-4">
                <select class="form-select" id="availabilityFilter" aria-label="Availability Filter">
                    <option value="ShowAll" selected>Show All</option>
                    <option value="Yes">Available</option>
                    <option value="No">Not Available</option>
                </select>
            </div>

            <!-- Vehicle Table -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="carTable">
                                <thead>
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Model</th>
                                        <th scope="col">Brand</th>
                                        <th scope="col">Available</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicleTableBody">
                                    <?php include "fetch_cars.php"; ?> <!-- PHP to fetch cars initially -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="back-btn-container">
                <button type="button" class="btn btn-primary" onclick="goToAddVehicle()" style="font-size: 1rem; margin-top:10px;">Back</button>
            </div>
        </div>

    </div>


    <script>
        function goToAddVehicle() {
            window.location.href = 'index.php?content=add_vehicle_content.php';
        }

        // AJAX function to fetch filtered cars
        function fetchFilteredCars() {
            const searchQuery = document.getElementById('searchInput').value;
            const availabilityFilter = document.getElementById('availabilityFilter').value;
            
            // Create a FormData object to send to the PHP script
            const formData = new FormData();
            formData.append('searchQuery', searchQuery);
            formData.append('availabilityFilter', availabilityFilter);
            
            // Send an AJAX request to fetch the filtered cars
            fetch('fetch_cars.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Update the table body with the new rows
                document.getElementById('vehicleTableBody').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        }

        // Event listener for the search bar
        document.getElementById('searchInput').addEventListener('input', fetchFilteredCars);

        // Event listener for the availability filter
        document.getElementById('availabilityFilter').addEventListener('change', fetchFilteredCars);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
