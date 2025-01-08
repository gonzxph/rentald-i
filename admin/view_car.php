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

        // Fetch all images associated with the car
        $q_images = "SELECT * FROM `car_image` WHERE `car_id` = '$car_id'";
        $result_images = mysqli_query($conn, $q_images);
        $car_images = $result_images && mysqli_num_rows($result_images) > 0 ? mysqli_fetch_all($result_images, MYSQLI_ASSOC) : [];
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

        // Handle image uploads
        if (isset($_FILES['image_upload']) && !empty($_FILES['image_upload']['name'][0])) {
            $img_uploaded_at = date('Y-m-d H:i:s');
            $img_folder = "../upload/car/";

            // Create directory if it doesn't exist
            if (!file_exists($img_folder)) {
                mkdir($img_folder, 0777, true);
            }

            foreach ($_FILES['image_upload']['name'] as $index => $img_name) {
                $img_tmp_name = $_FILES['image_upload']['tmp_name'][$index];
                
                // Generate unique filename
                $file_extension = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                $new_filename = 'car_' . rand(1000, 9999) . '_' . uniqid() . '.' . $file_extension;
                $img_path = $img_folder . $new_filename;

                // Validate file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = mime_content_type($img_tmp_name);

                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Invalid file type: " . $img_name);
                }

                if (!move_uploaded_file($img_tmp_name, $img_path)) {
                    throw new Exception("Failed to upload image: " . $img_name);
                }

                // Insert image details - store only filename
                $q_image_insert = "INSERT INTO `car_image` 
                                   (`car_id`, `img_url`, `img_description`, `img_position`, `is_primary`, `img_uploaded_at`) 
                                   VALUES (?, ?, ?, 0, 0, ?)";

                $stmt = mysqli_prepare($conn, $q_image_insert);
                mysqli_stmt_bind_param($stmt, 'isss', $car_id, $new_filename, $car_description, $img_uploaded_at);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error saving image details to database: " . mysqli_error($conn));
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
    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
    <title>D&I CEBU CAR RENTAL - View Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="view_car.css">
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    <div class="outer-box" style="margin-top:15px;">
        <!-- Success and Error Messages -->
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

        <h1 class="text-center mb-4">Vehicle Details</h1>
        <div class="container">
            <div class="content-container">
                <!-- Display all images -->
                <div class="text-center mb-4">
                    <?php if (!empty($car_images)): ?>
                        <?php foreach ($car_images as $image): ?>
                            <img src="../upload/car/<?php echo htmlspecialchars($image['img_url']); ?>" 
                                 alt="Car Image" class="img-fluid m-2" style="max-height: 200px; max-width: 300px; object-fit: contain;">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No images available for this vehicle.</p>
                    <?php endif; ?>
                </div>

                <!-- Vehicle Details Form -->
                <form id="viewVehicleForm" action="view_car.php?car_id=<?php echo $car_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="row g-3 justify-content-center">
                        <!-- Existing fields -->
                        <div class="col-md-5">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $car['car_brand']; ?>" disabled>
                        </div>
                        <!-- Car Model -->
                        <div class="col-md-5">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="<?php echo $car['car_model']; ?>" disabled>
                        </div>

                        <!-- Car Year -->
                        <div class="col-md-5">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" value="<?php echo $car['car_year']; ?>" disabled>
                        </div>

                        <!-- Car Color -->
                        <div class="col-md-5">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color" value="<?php echo $car['car_color']; ?>" disabled>
                        </div>

                        <!-- Car Type -->
                        <div class="col-md-5">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control" id="type" name="type" disabled>
                                <option value="Sedan" <?php echo ($car['car_type'] == 'Sedan') ? 'selected' : ''; ?>>Sedan</option>
                                <option value="SUV" <?php echo ($car['car_type'] == 'SUV') ? 'selected' : ''; ?>>SUV</option>
                                <option value="MPV" <?php echo ($car['car_type'] == 'MPV') ? 'selected' : ''; ?>>MPV</option>
                                <option value="Pick-up Truck" <?php echo ($car['car_type'] == 'Pick-up Truck') ? 'selected' : ''; ?>>Pick-up Truck</option>
                                <option value="Hatchback" <?php echo ($car['car_type'] == 'Hatchback') ? 'selected' : ''; ?>>Hatchback</option>
                                <option value="Crossover" <?php echo ($car['car_type'] == 'Crossover') ? 'selected' : ''; ?>>Crossover</option>
                                <option value="Sports Car" <?php echo ($car['car_type'] == 'Sports Car') ? 'selected' : ''; ?>>Sports Car</option>
                                <option value="Electric Vehicle" <?php echo ($car['car_type'] == 'Electric Vehicle') ? 'selected' : ''; ?>>Electric Vehicle</option>
                            </select>
                        </div>

                        <!-- Seats -->
                        <div class="col-md-5">
                            <label for="seats" class="form-label">Seats</label>
                            <input type="number" class="form-control" id="seats" name="seats" value="<?php echo $car['car_seats']; ?>" disabled>
                        </div>

                        <!-- Transmission -->
                        <div class="col-md-5">
                            <label for="transmission" class="form-label">Transmission</label>
                            <select class="form-select" id="transmission" name="transmission" disabled>
                                <option value="Automatic" <?php echo ($car['car_transmission_type'] == 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
                                <option value="Manual" <?php echo ($car['car_transmission_type'] == 'Manual') ? 'selected' : ''; ?>>Manual</option>
                            </select>
                        </div>

                        <!-- Fuel Type -->
                        <div class="col-md-5">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="fuel_type" name="fuel_type" disabled>
                                <option value="Diesel" <?php echo ($car['car_fuel_type'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Regular Unleaded" <?php echo ($car['car_fuel_type'] == 'Regular Unleaded') ? 'selected' : ''; ?>>Regular Unleaded</option>
                            </select>
                        </div>

                        <!-- Rental Rate -->
                        <div class="col-md-5">
                            <label for="rental_rate" class="form-label">Rental Rate</label>
                            <input type="number" class="form-control" id="rental_rate" name="rental_rate" value="<?php echo $car['car_rental_rate']; ?>" disabled>
                        </div>

                        <!-- Excess Hour -->
                        <div class="col-md-5">
                            <label for="excess_hour" class="form-label">Excess Per Hour</label>
                            <input type="number" class="form-control" id="excess_hour" name="excess_hour" value="<?php echo $car['car_excess_per_hour']; ?>" disabled>
                        </div>
                        <!-- Availability -->
                        <div class="col-md-5">
                            <label for="availability" class="form-label">Availability</label>
                            <select class="form-control" id="availability" name="availability" disabled>
                                <option value="1" <?php echo ($car['car_availability'] == 1 ? 'selected' : ''); ?>>Yes</option>
                                <option value="0" <?php echo ($car['car_availability'] == 0 ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" disabled><?php echo $car['car_description']; ?></textarea>
                        </div>
                       

                        <!-- Image Upload Section -->
                        <!-- Add this in the form section where you want the image upload -->
                        <div class="col-md-5" id="imageUploadSection" style="display: none;">
                            <h6 class="mb-4">Upload Vehicle Images</h6>
                            <div class="upload-container">
                                <div class="upload-button">
                                    <label for="image_upload" class="btn btn-primary btn-sm">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                        </svg>
                                        Upload Photos
                                    </label>
                                    <input type="file" name="image_upload[]" id="image_upload" multiple accept="image/*" class="d-none">
                                </div>
                                <div class="file-count mt-2 text-muted"></div>
                                <div class="preview-container mt-3">
                                    <!-- Existing images will be shown here -->
                                    <?php if (!empty($car_images)): ?>
                                        <?php foreach ($car_images as $image): ?>
                                            <div class="preview-item">
                                                <img src="../upload/car/<?php echo htmlspecialchars($image['img_url']); ?>" 
                                                    alt="Car Image">
                                                <p><?php echo htmlspecialchars($image['img_url']); ?></p>
                                                <button type="button" class="remove-btn" data-name="<?php echo htmlspecialchars($image['img_url']); ?>">×</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-warning" id="editButton">Edit</button>
                        <button type="submit" class="btn btn-primary d-none" id="saveButton" name="saveChanges">Save Changes</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='car_list.php';">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
         const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const inputs = document.querySelectorAll('#viewVehicleForm input, #viewVehicleForm textarea, #viewVehicleForm select');
        const imageUploadSection = document.getElementById('imageUploadSection');

        editButton.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = false);
            editButton.classList.add('d-none');
            saveButton.classList.remove('d-none');
            imageUploadSection.style.display = 'block'; // Show the image upload section
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('image_upload');
            const previewContainer = document.querySelector('.preview-container');
            const fileCount = document.querySelector('.file-count');
            let fileList = new DataTransfer();

            if (fileInput && previewContainer && fileCount) {
                // Initialize the file count with existing images
                updateFileCount();

                // Handle existing images
                const existingPreviews = document.querySelectorAll('.preview-item');
                existingPreviews.forEach(preview => {
                    const removeBtn = preview.querySelector('.remove-btn');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const imageName = this.getAttribute('data-name');
                            if (confirm('Are you sure you want to remove this image?')) {
                                // Add AJAX call to delete image from server
                                fetch('delete_car_image.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `car_id=<?php echo $car_id; ?>&image_name=${imageName}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        preview.remove();
                                        updateFileCount();
                                    } else {
                                        alert('Error removing image: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error removing image');
                                });
                            }
                        });
                    }
                });

                // Handle new file uploads
                fileInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    
                    files.forEach(file => {
                        fileList.items.add(file);
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="${file.name}">
                                <p>${file.name}</p>
                                <button type="button" class="remove-btn" data-name="${file.name}">×</button>
                            `;
                            
                            previewContainer.appendChild(previewItem);

                            // Add click handler for the new remove button
                            const removeBtn = previewItem.querySelector('.remove-btn');
                            removeBtn.addEventListener('click', function() {
                                previewItem.remove();
                                
                                // Update fileList
                                const newFileList = new DataTransfer();
                                const currentFiles = fileList.files;
                                for (let i = 0; i < currentFiles.length; i++) {
                                    if (currentFiles[i].name !== file.name) {
                                        newFileList.items.add(currentFiles[i]);
                                    }
                                }
                                fileList = newFileList;
                                fileInput.files = fileList.files;
                                updateFileCount();
                            });
                        }
                        
                        reader.readAsDataURL(file);
                    });

                    fileInput.files = fileList.files;
                    updateFileCount();
                });
            }

            function updateFileCount() {
                if (fileCount) {
                    const existingImageCount = document.querySelectorAll('.preview-item').length;
                    fileCount.textContent = `${existingImageCount} Files Selected`;
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
