<?php
session_start();
include "db_conn.php";

// Fetch car details 
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    $q_car = "SELECT * FROM `car` WHERE `car_id` = '$car_id'";
    $result_car = mysqli_query($conn, $q_car);

    if ($result_car && mysqli_num_rows($result_car) > 0) {
        $car = mysqli_fetch_assoc($result_car);

        $q_image = "SELECT * FROM `car_image` WHERE `car_id` = '$car_id' AND `is_primary` = 1";
        $result_image = mysqli_query($conn, $q_image);
        $car_image = $result_image && mysqli_num_rows($result_image) > 0 ? mysqli_fetch_assoc($result_image) : null;
    } else {
        $_SESSION['error'] = "Car not found.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "No car ID provided.";
    header("Location: index.php");
    exit();
}

// Handle form submission for updating the car details
if (isset($_POST['saveChanges'])) {
    // Start transaction
    mysqli_begin_transaction($conn);
    echo 'AASASASAS';
    
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

        // Update car details using prepared statement
        $q_update = "UPDATE `car` SET 
                     `car_description` = ?, `car_brand` = ?, 
                     `car_model` = ?, `car_year` = ?, `car_type` = ?,
                     `car_color` = ?, `car_seats` = ?, 
                     `car_transmission_type` = ?, `car_fuel_type` = ?, 
                     `car_rental_rate` = ?, `car_excess_per_hour` = ?, 
                     `car_availability` = ? WHERE `car_id` = ?";

        $stmt = mysqli_prepare($conn, $q_update);
        mysqli_stmt_bind_param($stmt, 'sssississsssi', 
            $car_description, $car_brand, $car_model, $car_year, $car_type,
            $car_color, $car_seats, $car_transmission_type, $car_fuel_type,
            $car_rental_rate, $car_excess_per_hour, $car_availability, $car_id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating car: " . mysqli_error($conn));
        }

        // Handle image update if a new image is uploaded
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $img_tmp_name = $_FILES['image_upload']['tmp_name'];
            $img_folder = "../upload/car/";
            
            // Create directory if it doesn't exist
            if (!file_exists($img_folder)) {
                mkdir($img_folder, 0777, true);
            }

            // Get old image details to delete later
            $q_old_image = "SELECT img_url FROM car_image WHERE car_id = ? AND is_primary = 1";
            $stmt = mysqli_prepare($conn, $q_old_image);
            mysqli_stmt_bind_param($stmt, 'i', $car_id);
            mysqli_stmt_execute($stmt);
            $old_image = mysqli_stmt_get_result($stmt)->fetch_assoc();

            // Generate unique filename - ensure consistent format
            $file_extension = strtolower(pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION));
            $new_filename = 'car_' . rand(1000, 9999) . '_' . uniqid() . '.' . $file_extension;

            $img_path = $img_folder . $new_filename;
 

            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($img_tmp_name);

            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid file type: " . $_FILES['image_upload']['name']);
            }

            if (!move_uploaded_file($img_tmp_name, $img_path)) {
                throw new Exception("Failed to upload image");
            }

            $img_uploaded_at = date('Y-m-d H:i:s');

            // Update image details in database - store ONLY the filename
            $q_image_update = "UPDATE `car_image` SET 
                              `img_url` = ?, 
                              `img_uploaded_at` = ? 
                              WHERE `car_id` = ? AND `is_primary` = 1";

            $stmt = mysqli_prepare($conn, $q_image_update);
            mysqli_stmt_bind_param($stmt, 'ssi', $new_filename, $img_uploaded_at, $car_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating image in database: " . mysqli_error($conn));
            }

            // Delete old image file if it exists
            if ($old_image && $old_image['img_url']) {
                $old_file_path = $img_folder . $old_image['img_url'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
        }

        // If everything is successful, commit the transaction
        mysqli_commit($conn);
        $_SESSION['success'] = "Vehicle updated successfully!";
        
    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: view_car.php?car_id=$car_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';  
    ?>
    <title>D&I CEBU CAR RENTAL - View Car</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="add_car.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <h1 class="text-center mb-4">View Vehicle</h1>

    <div class="container-fluid">
        <div class="content-container">
            <?php if ($car_image): ?>
                <div class="text-center mb-4">
                    <img src="<?php echo $car_image['img_url']; ?>" alt="Car Image" class="img-fluid" style="max-height: 300px;">
                </div>
            <?php endif; ?>

            <form id="viewVehicleForm" action="view_car.php?car_id=<?php echo $car_id; ?>" method="post" enctype="multipart/form-data">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-5">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $car['car_brand']; ?>" disabled>
                    </div>

                    <div class="col-md-5">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" value="<?php echo $car['car_model']; ?>" disabled>
                    </div>

                    <div class="col-md-5">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="year" name="year" value="<?php echo $car['car_year']; ?>" disabled>
                    </div>

                    <div class="col-md-5">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color" value="<?php echo $car['car_color']; ?>" disabled>
                    </div>

                    <div class="col-md-5">
                        <label for="type" class="form-label">Type</label>
                        <input type="text" class="form-control" id="type" name="type" value="<?php echo $car['car_type']; ?>" disabled>
                    </div>

                    <div class="col-md-5">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" disabled><?php echo $car['car_description']; ?></textarea>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-warning" id="editButton">Edit</button>
                    <button type="submit" class="btn btn-primary d-none" id="saveButton" name="saveChanges">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const inputs = document.querySelectorAll('#viewVehicleForm input, #viewVehicleForm textarea');

        editButton.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = false);
            editButton.classList.add('d-none');
            saveButton.classList.remove('d-none');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
