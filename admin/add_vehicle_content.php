<?php
session_start(); 
include "db_conn.php"; 


if (isset($_POST["addVehicle"])) {
  
    $car_description = $_POST["description"];
    $car_brand = $_POST["brand"];
    $car_model = $_POST["model"];
    $car_year = $_POST["year"];
    $car_type = $_POST["type"];  
    $car_color = $_POST["color"];
    $car_seats = $_POST["seats"];
    $car_transmission_type = $_POST["transmission"]; 
    $car_fuel_type = $_POST["fuel_type"];  
    $car_rental_rate = $_POST["rental_rate"];
    $car_excess_per_hour = $_POST["excess_hour"];
    $car_availability = $_POST["availability"];  

    if (empty($car_description) || empty($car_brand) || empty($car_model) || empty($car_year) || empty($car_type) || 
        empty($car_color) || empty($car_seats) || empty($car_transmission_type) || empty($car_fuel_type) || 
        empty($car_rental_rate) || empty($car_excess_per_hour) || empty($car_availability)) {
        $_SESSION['error'] = "All fields are required. Please fill in the missing information.";
        header("Location: index.php?content=add_vehicle_content.php");
        exit();
    }

   
    $q_car = "INSERT INTO car 
              (car_description, car_brand, car_model, car_year, car_type, car_color, car_seats, 
               car_transmission_type, car_fuel_type, car_rental_rate, car_excess_per_hour, car_availability) 
              VALUES 
              ('$car_description', '$car_brand', '$car_model', '$car_year', '$car_type', '$car_color', '$car_seats', 
               '$car_transmission_type', '$car_fuel_type', '$car_rental_rate', '$car_excess_per_hour', '$car_availability')";

    if (mysqli_query($conn, $q_car)) {
        $car_id = mysqli_insert_id($conn); 

        // Handle file upload for the image
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $img_description = $car_description;
            $img_position = 1; 
            $is_primary = 1;   
            $img_uploaded_at = date('Y-m-d H:i:s');

            // Process the uploaded file
            $img_name = $_FILES['image_upload']['name'];
            $img_tmp_name = $_FILES['image_upload']['tmp_name'];
            $img_folder = "uploads/";
            $img_path = $img_folder . basename($img_name);

            // Move uploaded file to the target folder
            if (move_uploaded_file($img_tmp_name, $img_path)) {
                // Insert image data into car_image table
                $q_image = "INSERT INTO car_image 
                            (car_id, img_url, img_description, img_position, is_primary, img_uploaded_at) 
                            VALUES 
                            ('$car_id', '$img_path', '$img_description', '$img_position', '$is_primary', '$img_uploaded_at')";
                if (!mysqli_query($conn, $q_image)) {
                    $_SESSION['error'] = "Error inserting image: " . mysqli_error($conn);
                    header("Location: index.php?content=add_vehicle_content.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header("Location: index.php?content=add_vehicle_content.php");
                exit();
            }
        }

        // Set session variable to show the success modal
        $_SESSION['success'] = "Vehicle added successfully!";
        header("Location: index.php?content=add_vehicle_content.php");
        exit();
    } else {
        $_SESSION['error'] = "Error inserting car: " . mysqli_error($conn);
        header("Location: index.php?content=add_vehicle_content.php");
        exit();
    }
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
        <a href="car_list.php" class="btn btn-primary">View Vehicle List</a>
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

                <!-- Image Upload -->
                <div class="col-md-5">
                    <label for="image_upload" class="form-label">Upload Image</label>
                    <input type="file" class="form-control" id="image_upload" name="image_upload" required>
                </div>

                <!-- Description -->
                <div class="col-md-5">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description" required></textarea>
                </div>
            </div>

            <!-- Form buttons -->
            <div class="modal-footer" style="margin-top:20px; margin-bottom:30px;">
                <button type="submit" class="btn btn-primary" name="addVehicle" style="margin-right:10px;">Add Vehicle</button>
            </div>
        </form>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
       


    </div>
</div>

</body>
</html>















