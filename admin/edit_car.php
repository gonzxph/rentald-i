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

    $q_update = "UPDATE `car` SET 
                 `car_description` = '$car_description', `car_brand` = '$car_brand', 
                 `car_model` = '$car_model', `car_year` = '$car_year', `car_type` = '$car_type',
                 `car_color` = '$car_color', `car_seats` = '$car_seats', 
                 `car_transmission_type` = '$car_transmission_type', `car_fuel_type` = '$car_fuel_type', 
                 `car_rental_rate` = '$car_rental_rate', `car_excess_per_hour` = '$car_excess_per_hour', 
                 `car_availability` = '$car_availability' WHERE `car_id` = '$car_id'";

    if (mysqli_query($conn, $q_update)) {
        // Handle image update if a new image is uploaded
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $img_name = $_FILES['image_upload']['name'];
            $img_tmp_name = $_FILES['image_upload']['tmp_name'];
            $img_folder = "uploads/";
            $img_path = $img_folder . basename($img_name);

            if (move_uploaded_file($img_tmp_name, $img_path)) {
                $img_uploaded_at = date('Y-m-d H:i:s');

                $q_image_update = "UPDATE `car_image` SET `img_url` = '$img_path', `img_uploaded_at` = '$img_uploaded_at' 
                                  WHERE `car_id` = '$car_id' AND `is_primary` = 1";

                if (!mysqli_query($conn, $q_image_update)) {
                    $_SESSION['error'] = "Error updating image: " . mysqli_error($conn);
                    header("Location: view_car.php?car_id=$car_id");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header("Location: view_car.php?car_id=$car_id");
                exit();
            }
        }

        $_SESSION['success'] = "Vehicle updated successfully!";
        header("Location: view_car.php?car_id=$car_id");
        exit();
    } else {
        $_SESSION['error'] = "Error updating car: " . mysqli_error($conn);
        header("Location: view_car.php?car_id=$car_id");
        exit();
    }
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
