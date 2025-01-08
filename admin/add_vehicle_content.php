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
        error_log($car_availability);

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

            // Debug: Print number of files
            error_log("Number of files: " . count($_FILES['image_upload']['name']));

            // Loop through each uploaded file
            foreach ($_FILES['image_upload']['name'] as $i => $name) {
                $img_name = $_FILES['image_upload']['name'][$i];
                $img_tmp_name = $_FILES['image_upload']['tmp_name'][$i];
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

                // Insert image details - simplified query
                $q_image_insert = "INSERT INTO `car_image` (`car_id`, `img_url`, `img_description`) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $q_image_insert);
                mysqli_stmt_bind_param($stmt, 'iss', $car_id, $fileName, $car_description);
                
                // Debug: Print query details
                error_log("Inserting image: " . $fileName . " for car_id: " . $car_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error saving image details to database: " . mysqli_error($conn));
                }
            }
        }

        // If everything is successful, commit the transaction
        mysqli_commit($conn);
        
        $_SESSION['success'] = "Vehicle added successfully!";
        
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
    <style>
        .upload-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .upload-button {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .upload-button label {
            background: #0066ff;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s ease;
        }

        .upload-button label:hover {
            background: #0052cc;
        }

        .file-count {
            text-align: center;
            margin-bottom: 1rem;
            color: #666;
        }

        .preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-item {
            position: relative;
        }

        .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .preview-item p {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
            word-break: break-all;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
    </style>
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
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-md-5">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the vehicle" required></textarea>
                </div>

                 <!-- Image Upload -->
                <div class="col-md-5">
                    <h6 class="mb-4">Upload Vehicle Images</h6>
                    <div class="upload-container">
                        <div class="upload-button">
                            <label for="image_upload" class="btn btn-primary btn-sm">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                </svg>
                                Upload Photos
                            </label>
                            <input type="file" name="image_upload[]" id="image_upload" multiple accept="image/*" class="d-none" required>
                        </div>
                        <div class="file-count mt-2 text-muted"></div>
                        <div class="preview-container mt-3"></div>

                    </div>
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

<script>
// Test if JavaScript is running
document.addEventListener('DOMContentLoaded', function() {
    alert('JavaScript is running in add_vehicle_content.php');
});
</script>

</body>
</html>