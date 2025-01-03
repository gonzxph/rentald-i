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
                <form class="d-flex" role="search" method="GET" action="">
                    <input class="form-control me-2" type="search" placeholder="Search by Model or Brand" aria-label="Search" name="searchQuery" value="<?php echo isset($_GET['searchQuery']) ? $_GET['searchQuery'] : ''; ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
            <!-- Availability Dropdown -->
            <div class="availability-container mb-4">
                <form id="availabilityForm" method="GET" action="">
                    <div class="row">
                        <div class="col-12 col-sm-8 col-md-4 col-lg-5">
                            <select id="availabilityFilter" class="form-select" name="availabilityFilter" style="font-size: 0.8rem; font-weight: bold;">
                                <option disabled selected style="color: rgb(255, 255, 255); background-color: rgb(0, 0, 0); font-size: 0.9rem;">Filter by Availability</option>
                                <option value="ShowAll" <?php echo isset($_GET['availabilityFilter']) && $_GET['availabilityFilter'] == 'ShowAll' ? 'selected' : ''; ?>>Show All</option>
                                <option value="Yes" <?php echo isset($_GET['availabilityFilter']) && $_GET['availabilityFilter'] == 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo isset($_GET['availabilityFilter']) && $_GET['availabilityFilter'] == 'No' ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>



            <style>
                input {
                    font-size: 1rem; 
                }

                input::placeholder {
                    font-size: 0.8em;
                }
            </style>

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
                                    include "db_conn.php";
                                    
                                    // search query and availability filter
                                    $searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
                                    $availabilityFilter = isset($_GET['availabilityFilter']) ? $_GET['availabilityFilter'] : 'ShowAll';
                                    
                                    // search condition for only Model and Brand
                                    if (!empty($searchQuery)) {
                                        $sql = "SELECT * FROM car WHERE (car_model LIKE ? OR car_brand LIKE ?)";
                                        if ($availabilityFilter != 'ShowAll') {
                                            $sql .= " AND car_availability = ?";
                                        }
                                        $stmt = $conn->prepare($sql);
                                        $searchTerm = "%" . $searchQuery . "%"; 
                                        if ($availabilityFilter != 'ShowAll') {
                                            $stmt->bind_param('sss', $searchTerm, $searchTerm, $availabilityFilter);
                                        } else {
                                            $stmt->bind_param('ss', $searchTerm, $searchTerm);
                                        }
                                    } else {
                                        $sql = "SELECT * FROM car";
                                        if ($availabilityFilter != 'ShowAll') {
                                            $sql .= " WHERE car_availability = ?";
                                        }
                                        $stmt = $conn->prepare($sql);
                                        if ($availabilityFilter != 'ShowAll') {
                                            $stmt->bind_param('s', $availabilityFilter);
                                        }
                                    }

                                    // Execute the query
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if (!$result) {
                                        die("Invalid Query!");
                                    }

                                    // Display the results
                                    while ($row = $result->fetch_assoc()) {
                                        echo '
                                        <tr>
                                            <th>' . $row['car_id'] . '</th>
                                            <td>' . $row['car_model'] . '</td>
                                            <td>' . $row['car_brand'] . '</td>
                                            <td>' . $row['car_availability'] . '</td>
                                            <td>
                                                <a href="view_car.php?car_id=' . $row['car_id'] . '" class="btn btn-primary"><i class="far fa-eye"></i></a>
                                                <a href="delete_car.php?car_id=' . $row['car_id'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this vehicle?\');"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>';
                                    }
                                    ?>
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
    </script>

    <script>
        document.getElementById('availabilityFilter').addEventListener('change', function () {
            // Submit the form automatically when the dropdown value changes
            document.getElementById('availabilityForm').submit();
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
