<?php
session_start();
include "db_conn.php";

if (isset($_POST['addVehicle'])) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        $car_description = $_POST['description'];
        $car_brand = $_POST['brand'];
        $car_model = $_POST['model'];
        $car_year = $_POST['year'];
        $car_type = $_POST['type'];
        $car_color = $_POST['color'];
        $car_seats = $_POST['seats'];
        $car_transmission_type = $_POST['transmission'];
        $car_fuel_type = $_POST['fuel_type'];
        $car_rental_rate = $_POST['rental_rate'];
        $car_excess_per_hour = $_POST['excess_hour'];
        $car_availability = $_POST['availability'];

        // Insert vehicle details into the database
        $q_add_car = "INSERT INTO `car` 
                      (`car_description`, `car_brand`, `car_model`, `car_year`, `car_type`, 
                       `car_color`, `car_seats`, `car_transmission_type`, `car_fuel_type`, 
                       `car_rental_rate`, `car_excess_per_hour`, `car_availability`) 
                      VALUES 
                      (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                      
        $stmt = mysqli_prepare($conn, $q_add_car);
        mysqli_stmt_bind_param($stmt, 'sssississsss', 
            $car_description, $car_brand, $car_model, $car_year, $car_type,
            $car_color, $car_seats, $car_transmission_type, $car_fuel_type,
            $car_rental_rate, $car_excess_per_hour, $car_availability
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error adding vehicle: " . mysqli_error($conn));
        }

        $car_id = mysqli_insert_id($conn);
        $uploaded_files = [];

        if (isset($_FILES['image_upload']) && !empty($_FILES['image_upload']['name'][0])) {
            $img_uploaded_at = date('Y-m-d H:i:s');
            $img_folder = "../upload/car/";

            // Create directory if it doesn't exist
            if (!file_exists($img_folder)) {
                mkdir($img_folder, 0777, true);
            }

            foreach ($_FILES['image_upload']['name'] as $index => $img_name) {
                $img_tmp_name = $_FILES['image_upload']['tmp_name'][$index];
                $file_extension = pathinfo($img_name, PATHINFO_EXTENSION);
                
                // Generate unique filename
                $fileName = uniqid('car_' . rand(1000, 9999) . '_') . '.' . $file_extension;
                $img_path = $img_folder . $fileName;

                // Validate file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = mime_content_type($img_tmp_name);

                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Invalid file type: " . $img_name);
                }

                if (!move_uploaded_file($img_tmp_name, $img_path)) {
                    throw new Exception("Failed to upload image: " . $img_name);
                }

                $uploaded_files[] = $fileName;

                // Insert image details
                $q_image_insert = "INSERT INTO `car_image` 
                                   (`car_id`, `img_url`, `img_description`, `img_position`, `is_primary`, `img_uploaded_at`) 
                                   VALUES (?, ?, ?, 0, 0, ?)";
                                   
                $stmt = mysqli_prepare($conn, $q_image_insert);
                mysqli_stmt_bind_param($stmt, 'isss', $car_id, $fileName, $car_description, $img_uploaded_at);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error saving image details to database: " . mysqli_error($conn));
                }
            }
        }

        // If everything is successful, commit the transaction
        mysqli_commit($conn);
        
        $_SESSION['success'] = "Vehicle added successfully!";
        if (!empty($uploaded_files)) {
            $_SESSION['success'] .= " Uploaded files: " . implode(", ", $uploaded_files);
        }
        
    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        mysqli_rollback($conn);
        
        // Delete any uploaded files if they exist
        if (!empty($uploaded_files)) {
            foreach ($uploaded_files as $file) {
                $file_path = $img_folder . $file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: index.php?content=add_vehicle_content.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="add_vehicle_content.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
        
<!-- Success Alert -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <!-- Error Alert -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-left">Add Vehicle</h1>
        <a href="car_list.php" class="btn btn-primary" style="font-size: 1rem;">View Vehicle List</a>
    </div>


    <div class="container-fluid">
        <div class="content-container">
        <form id="addVehicleForm" action="add_vehicle_content.php" method="post" enctype="multipart/form-data">
            <div class="row g-3 justify-content-center">
                <!-- Car Brand -->
                <div class="col-md-5">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter brand" required>
                </div>

                <!-- Car Model -->
                <div class="col-md-5">
                    <label for="model" class="form-label">Model</label>
                    <input type="text" class="form-control" id="model" name="model" placeholder="Enter model" required>
                </div>

                <!-- Car Year -->
                <div class="col-md-5">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" class="form-control" id="year" name="year" placeholder="Enter year" required>
                </div>

                <!-- Car Color -->
                <div class="col-md-5">
                    <label for="color" class="form-label">Color</label>
                    <input type="text" class="form-control" id="color" name="color" placeholder="Enter color" required>
                </div>

                <!-- Car Type -->
                <div class="col-md-5">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" disabled selected>Select car type</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="MPV">MPV</option>
                        <option value="Pick-up Truck">Pick-up Truck</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Crossover">Crossover</option>
                        <option value="Sports Car">Sports Car</option>
                        <option value="Electric Vehicle">Electric Vehicle</option>
                    </select>
                </div>

                <!-- Rental Rate -->
                <div class="col-md-5">
                    <label for="rental_rate" class="form-label">Rental Rate</label>
                    <input type="number" class="form-control" id="rental_rate" name="rental_rate" placeholder="Enter rate" required>
                </div>

                <!-- Seats -->
                <div class="col-md-5">
                    <label for="seats" class="form-label">Seats</label>
                    <input type="number" class="form-control" id="seats" name="seats" placeholder="Enter number of seats" required>
                </div>

                <!-- Transmission -->
                <div class="col-md-5">
                    <label for="transmission" class="form-label">Transmission</label>
                    <select class="form-select" id="transmission" name="transmission" required>
                        <option value="" disabled selected>Select Transmission</option>
                        <option value="Automatic">Automatic</option>
                        <option value="Manual">Manual</option>
                    </select>
                </div>

                <!-- Fuel Type -->
                <div class="col-md-5">
                    <label for="fuel_type" class="form-label">Fuel Type</label>
                    <select class="form-select" id="fuel_type" name="fuel_type" required>
                        <option value="" disabled selected>Select Fuel Type</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Regular Unleaded">Regular Unleaded</option>
                    </select>
                </div>

                <!-- Excess Per Hour -->
                <div class="col-md-5">
                    <label for="excess_hour" class="form-label">Excess Per Hour</label>
                    <input type="number" class="form-control" id="excess_hour" name="excess_hour" placeholder="Enter excess per hour" required>
                </div>

                <!-- Availability -->
                <div class="col-md-5">
                    <label for="availability" class="form-label">Availability</label>
                    <select class="form-select" id="availability" name="availability" required>
                        <option value="" disabled selected>Select availability</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-md-5">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the vehicle" required></textarea>
                </div>

                 <!-- Image Upload -->
                <div class="col-md-4">
                    <label for="image_upload" class="form-label">Upload Images</label>
                    <input type="file" name="image_upload[]" id="image_upload" class="form-control" multiple required>
                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple files.</small>
                </div>

                    
            </div>

                <!-- Form Buttons -->
                <div class="modal-footer" style="margin-top:20px; margin-bottom:30px;">
                    <div class="d-flex justify-content-between w-100">
                        <button type="reset" class="btn btn-secondary" style="font-size: 1rem;">Reset</button>
                        <button type="submit" class="btn btn-primary" name="addVehicle" style="font-size: 1rem;">Add Vehicle</button>
                      
                    </div>
                </div>




        </form>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
       


    </div>
</div>

</body>
</html>