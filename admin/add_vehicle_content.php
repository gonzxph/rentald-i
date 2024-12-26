<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="add_vehicle_content.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
        <!-- Header Section -->
        <div class="header-container mb-4">
            <h1 class="text-left mb-3">Add Vehicle</h1>
            <button id="addCarButton" class="add-car-button btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                + Add Car
            </button>
        </div>

        <!-- Search Bar -->
        <div class="search-container mb-4">
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>

        <!-- View sample output -->
        <div class="search-container mb-4">
            <a href="view_vehicle.php" class="btn btn-primary">
                <i class="far fa-eye"></i> View
            </a>
        </div>

        <!-- Edit sample output -->
        <div class="search-container mb-4">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editCarModal">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>

        <!-- Vehicle Table -->
        <div class="row g-4">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Model</th>
                                    <th scope="col">Brand</th>
                                    <th scope="col">Available</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once 'class/dbh.php';
                                require_once 'class/car.php';
                                require_once 'class/car_repository.php';

                                $carrep = new CarRepository();
                                $carlist = $carrep->getAllCars();
                                $i = 1;

                                foreach ($carlist as $row) {
                                    echo '<tr>
                                            <td>' . htmlspecialchars($i) . '</td>
                                            <td>' . htmlspecialchars($row->getModel()) . '</td>
                                            <td>' . htmlspecialchars($row->getBrand()) . '</td>
                                            <td>' . htmlspecialchars($row->getAvailable()) . '</td>
                                            <td>
                                                <div class="d-flex flex-column flex-sm-row gap-1">
                                                    <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                                                    <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>';
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Car Modal -->
<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCarModalLabel">Add New Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3 justify-content-center">
                        <!-- Brand and Model -->
                        <div class="col-md-5  ">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand" placeholder="Enter brand">
                        </div>
                        <div class="col-md-5 ">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" placeholder="Enter model">
                        </div>
                        
                        <!-- Year and Colour -->
                        <div class="col-md-5 ">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" placeholder="Enter year">
                        </div>
                        <div class="col-md-5  ">
                            <label for="colour" class="form-label">Colour</label>
                            <input type="text" class="form-control" id="colour" placeholder="Enter colour">
                        </div>
                    
                        <!-- License Plate and VIN -->
                        <div class="col-md-5 ">
                            <label for="licensePlate" class="form-label">License Plate</label>
                            <input type="text" class="form-control" id="licensePlate" placeholder="Enter license plate">
                        </div>
                        <div class="col-md-5 ">
                            <label for="vin" class="form-label">VIN</label>
                            <input type="text" class="form-control" id="vin" placeholder="Enter VIN">
                        </div>
                        
                        <!-- Seats Transmission Type -->
                        <div class="col-md-5">
                            <label for="seats" class="form-label">Seats</label>
                            <input type="number" class="form-control" id="seats" placeholder="Enter number of seats">
                        </div>
                        <div class="col-md-5 ">
                            <label for="transmission" class="form-label">Transmission Type</label>
                            <input type="text" class="form-control" id="transmission" placeholder="Automatic/Manual">
                        </div>

                        <!-- Fuel Type Availability -->
                        <div class="col-md-5 ">
                            <label for="fuelType" class="form-label">Fuel Type</label>
                            <input type="text" class="form-control" id="fuelType" placeholder="Enter fuel type">
                        </div>
                        <div class="col-md-5  ">
                            <label class="form-label d-block">Availability</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="availability" id="availableYes" value="Yes">
                                <label class="form-check-label" for="availableYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="availability" id="availableNo" value="No">
                                <label class="form-check-label" for="availableNo">No</label>
                            </div>
                        </div>
                    
                        <!-- Image Upload and Description -->
                        <div class="col-md-5 ">
                            <label for="imageUpload" class="form-label">Upload Image</label>
                            <input type="file" class="form-control" id="imageUpload">
                        </div>
                        <div class="col-md-5 ">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3" placeholder="Enter vehicle description"></textarea>
                        </div>
                    
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Vehicle</button>
            </div>
        </div>
    </div>
</div>





<!-- Edit Car Details Modal -->
<div class="modal fade" id="editCarModal" tabindex="-1" aria-labelledby="editCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCarModalLabel">Edit Car Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3 justify-content-center">
                        <!-- Brand -->
                        <div class="col-md-5">
                            <label for="editBrand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="editBrand" value="Toyota">
                        </div>
                        <!-- Model -->
                        <div class="col-md-5">
                            <label for="editModel" class="form-label">Model</label>
                            <input type="text" class="form-control" id="editModel" value="Corolla">
                        </div>
                        <!-- Year -->
                        <div class="col-md-5">
                            <label for="editYear" class="form-label">Year</label>
                            <input type="number" class="form-control" id="editYear" value="2021">
                        </div>
                        <!-- Colour -->
                        <div class="col-md-5">
                            <label for="editColour" class="form-label">Colour</label>
                            <input type="text" class="form-control" id="editColour" value="White">
                        </div>
                        <!-- License Plate -->
                        <div class="col-md-5">
                            <label for="editLicensePlate" class="form-label">License Plate</label>
                            <input type="text" class="form-control" id="editLicensePlate" value="ABC-1234">
                        </div>
                        <!-- VIN -->
                        <div class="col-md-5">
                            <label for="editVIN" class="form-label">VIN</label>
                            <input type="text" class="form-control" id="editVIN" value="1HGBH41JXMN109186">
                        </div>
                        <!-- Seats -->
                        <div class="col-md-5">
                            <label for="editSeats" class="form-label">Seats</label>
                            <input type="number" class="form-control" id="editSeats" value="5">
                        </div>
                        <!-- Transmission Type -->
                        <div class="col-md-5">
                            <label for="editTransmission" class="form-label">Transmission Type</label>
                            <input type="text" class="form-control" id="editTransmission" value="Automatic">
                        </div>
                        <!-- Fuel Type -->
                        <div class="col-md-5">
                            <label for="editFuelType" class="form-label">Fuel Type</label>
                            <input type="text" class="form-control" id="editFuelType" value="Petrol">
                        </div>
                        <!-- Availability -->
                        <div class="col-md-5">
                            <label class="form-label d-block">Availability</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="editAvailability" id="editAvailableYes" value="Yes" checked>
                                <label class="form-check-label" for="editAvailableYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="editAvailability" id="editAvailableNo" value="No">
                                <label class="form-check-label" for="editAvailableNo">No</label>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="col-md-5">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" rows="3">Good for daily commutes.</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
