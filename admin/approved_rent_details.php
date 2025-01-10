<?php
// Include necessary files
include 'db_conn.php'; 
session_start();

// Check if user is not logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role'])) {
    header('Location: ../signin.php');
    exit();
}

// Check if user is neither ADMIN nor AGENT
if($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'AGENT') {
    header('Location: ../signin.php');
    exit();
}

// Check for rental_id in the query parameter
$rental_id = $_GET['rental_id'] ?? null;
if (empty($rental_id)) {
    header("Location: booking_content.php?error=invalid_id");
    exit();
}

// Fetch the rental details
$sql = "
    SELECT 
        r.rent_status,
        r.rent_pickup_datetime,
        r.rent_pickup_location,
        r.rent_dropoff_datetime,
        r.rent_dropoff_location,
        r.is_custom_driver,
        r.rent_approved_by,
        r.rent_approved_datetime,
        u.user_fname,
        u.user_mname,
        u.user_lname,
        u.user_email,
        u.user_phone,
        approver.user_fname as approver_fname,
        approver.user_mname as approver_mname,
        approver.user_lname as approver_lname,
        p.pay_type,
        p.pay_rental_charge,
        p.pay_pickup_charge,
        p.pay_dropoff_charge,
        p.pay_reservation_fee,
        p.pay_total_due,
        p.pay_amount_paid,
        p.pay_balance_due,
        c.car_brand,
        c.car_model,
        c.car_year,
        c.car_type,
        c.car_color,
        c.car_seats,
        c.car_transmission_type,
        c.car_fuel_type,
        ci.img_url,
        r.custom_driver_name AS driver_name,
        r.custom_driver_phone AS driver_phone,
        r.custom_driver_license_number AS driver_license_number
    FROM rental r
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    LEFT JOIN car c ON r.car_id = c.car_id
    LEFT JOIN car_image ci ON c.car_id = ci.car_id AND ci.is_primary = 1
    LEFT JOIN driver d ON r.assigned_driver_id = d.driver_id
    LEFT JOIN user u ON r.user_id = u.user_id
    LEFT JOIN user approver ON r.rent_approved_by = approver.user_id
    WHERE r.rental_id = ? AND r.rent_status = 'APPROVED'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Handle penalty form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $charge_type = $_POST['charge_type'] ?? null;
    $charge_amount = $_POST['charge'] ?? null;
    $description = $_POST['description'] ?? null;
    $date = date('Y-m-d H:i:s');

    // Validate form inputs
    if (empty($charge_type) || empty($charge_amount) || empty($description) || empty($rental_id)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: approved_rent_details.php?rental_id=$rental_id");
        exit();
    }

    // Insert penalty into the database
    $sql = "
        INSERT INTO rent_penalty (rental_id, rent_penalty_type, rent_penalty_amount, rent_penalty_description, rent_penalty_date)
        VALUES (?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isdss", $rental_id, $charge_type, $charge_amount, $description, $date);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Penalty or additional charge successfully added.';
        } else {
            $_SESSION['error'] = 'Failed to add penalty or additional charge.';
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: Unable to prepare statement.';
    }

    header("Location: approved_rent_details.php?rental_id=$rental_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
    <title>D&I CEBU CAR RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="approved_rent_details.css">
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="content-container">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

               <!-- Renter -->
               <section class="renter-info" style="margin-top: 20px;">
                <h2>Renter</h2>
                <p>Full Name: <span>
                    <?php
                    // Check if the first, middle, and last names are available
                    $full_name = '';
                    
                    if (!empty($data['user_fname'])) {
                        $full_name .= $data['user_fname'];
                    }
                    
                    if (!empty($data['user_mname'])) {
                        if (!empty($full_name)) {
                            $full_name .= ' ';
                        }
                        $full_name .= $data['user_mname'];
                    }
                    
                    if (!empty($data['user_lname'])) {
                        if (!empty($full_name)) {
                            $full_name .= ' ';
                        }
                        $full_name .= $data['user_lname'];
                    }
                    
                    // If the full name is empty, set it to 'N/A'
                    echo $full_name ?: 'N/A';
                    ?>
                </span></p>
                <p>Email: <span><?= $data['user_email'] ?? 'N/A'; ?></span></p>
                <p>Phone Number: <span><?= $data['user_phone'] ?? 'N/A'; ?></span></p>
            </section>

            <!-- Choosen Car-->
            <section class="choosen-car">
                <h2>Choosen Car</h2>
                <p>Vehicle: <span><?= isset($data['car_brand'], $data['car_model']) ? $data['car_brand'] . ' ' . $data['car_model'] : 'N/A'; ?></span></p>
                <p>Year: <span><?= $data['car_year'] ?? 'N/A'; ?></span></p>
                <p>Type: <span><?= $data['car_type'] ?? 'N/A'; ?></span></p>
                <p>Color: <span><?= $data['car_color'] ?? 'N/A'; ?></span></p>
                <p>Seats: <span><?= $data['car_seats'] ?? 'N/A'; ?></span></p>
                <p>Transmission Type: <span><?= $data['car_transmission_type'] ?? 'N/A'; ?></span></p>
                <p>Fuel Type: <span><?= $data['car_fuel_type'] ?? 'N/A'; ?></span></p>
            </section>

            <!-- Booking Status Section -->
            <section class="booking-status" style="margin-top: 20px;">
                <h2>Booking Status</h2>
                <p class="status-text"><?= $data['rent_status'] ?? 'N/A'; ?></p>
            </section>

            <!-- Approval Details -->
            <section class="approval-details" style="margin-top: 20px;">
                <h2>Approval Details</h2>
                <p>Approved By: <span>
                    <?php
                    $approver_name = '';
                    if (!empty($data['approver_fname'])) {
                        $approver_name .= $data['approver_fname'];
                        if (!empty($data['approver_mname'])) {
                            $approver_name .= ' ' . $data['approver_mname'];
                        }
                        if (!empty($data['approver_lname'])) {
                            $approver_name .= ' ' . $data['approver_lname'];
                        }
                        echo $approver_name;
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span></p>
                <p>Approval Date: <span><?= $data['rent_approved_datetime'] ?? 'N/A'; ?></span></p>
            </section>
            
            <!-- Pickup and Dropoff Details -->
            <section class="pickup-dropoff">
                <div class="pickup">
                    <h2>Pick-up Details</h2>
                    <p>Date & Time: <span><?= $data['rent_pickup_datetime'] ?? 'N/A'; ?></span></p>
                    <p>Address: <span><?= $data['rent_pickup_location'] ?? 'N/A'; ?></span></p>
                </div>
                <div class="dropoff">
                    <h2>Drop-off Details</h2>
                    <p>Date & Time: <span><?= $data['rent_dropoff_datetime'] ?? 'N/A'; ?></span></p>
                    <p>Address: <span><?= $data['rent_dropoff_location'] ?? 'N/A'; ?></span></p>
                </div>
            </section>

            <!-- Payment Option -->
            <section class="payment-option">
                <h2>Payment Option</h2>
                <p>Method: <span><?= $data['pay_type'] ?? 'N/A'; ?></span></p>
            </section>

            <!-- Rental Type -->
            <section class="rental-type">
                <h2>Rental Type</h2>
                <p>Type: <span><?= $data['is_custom_driver'] ? 'Self Drive' : 'With Driver'; ?></span></p>
            </section>

            
            <!-- Driver Information -->
            <section class="driver-information">
                <h2>Driver Information</h2>
                <?php
                // Check if the rental has a custom driver
                
                if ($data['is_custom_driver']) {
                    // Display custom driver details
                    echo "<p>Full Name: <span>" . ($data['driver_name'] ?? 'N/A') . "</span></p>";
                    echo "<p>Contact Number: <span>" . ($data['driver_phone'] ?? 'N/A') . "</span></p>";
                    echo "<p>Driver's License Number: <span>" . ($data['driver_license_number'] ?? 'N/A') . "</span></p>";
                } else {
                    // Query to fetch driver's full name, including middle name (if available)
                    $driverQuery = "
                        SELECT 
                            CONCAT(u.user_fname, ' ', IFNULL(u.user_mname, ''), ' ', u.user_lname) AS full_name
                        FROM user u
                        JOIN rental r ON u.user_id = r.assigned_driver_id
                        WHERE r.rental_id = ?
                    ";

                    $stmt = $conn->prepare($driverQuery);
                    $stmt->bind_param("i", $rental_id);
                    $stmt->execute();
                    $driverResult = $stmt->get_result();
                    $driverData = $driverResult->fetch_assoc();

                    // Display the assigned driver's full name from the query result
                    echo "<p>Full Name: <span>" . ($driverData['full_name'] ?? 'N/A') . "</span></p>";
                }

                // Only show the button if there is no custom driver
                if (!$data['is_custom_driver']) {
                    echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                            Assign Driver
                        </button>';
                }
                ?>
            </section>

            <!-- Modal for Assigning Driver -->
            <div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="assign_driver.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignDriverModalLabel">Assign Driver</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Driver Selection Dropdown -->
                                <div class="mb-3">
                                    <label for="driver" class="form-label">Select Driver</label>
                                    <select class="form-select" id="driver" name="driver_id" required>
                                        <option value="" disabled selected>Select a driver</option>
                                        <?php
                                        $driverQuery = "SELECT 
                                                        user_id, 
                                                        CONCAT(user_fname, ' ', COALESCE(user_mname, ''), ' ', user_lname) AS full_name 
                                                    FROM 
                                                        user 
                                                    WHERE 
                                                        user_role = 'DRIVER' 
                                                        AND user_status = 'ACTIVE'";
                                        $driverResult = $conn->query($driverQuery);
                                        
                                        if ($driverResult->num_rows > 0) {
                                            while ($driver = $driverResult->fetch_assoc()) {
                                                echo "<option value=\"{$driver['user_id']}\">{$driver['full_name']}</option>";
                                            }
                                        } else {
                                            echo "<option value=\"\" disabled>No drivers available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="rental_id" value="<?= htmlspecialchars($rental_id); ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Price Breakdown -->
            <section class="price-breakdown">
                <h2>Price Breakdown</h2>
                <p>Rental Charge: <span>PHP <?= number_format($data['pay_rental_charge'], 2) ?? '0.00'; ?></span></p>
                <p>Total Amount Due: <span>PHP <?= number_format($data['pay_total_due'], 2) ?? '0.00'; ?></span></p>
                <p>Amount Paid: <span>PHP <?= number_format($data['pay_amount_paid'], 2) ?? '0.00'; ?></span></p>
                <p>Balance Due: <span>PHP <?= number_format($data['pay_balance_due'], 2) ?? '0.00'; ?></span></p>
            </section>


            <!-- Penalty Form -->
            <section class="penalty-form">
                <h2>Add Penalty or Additional Charge</h2>
                <form action="approved_rent_details.php?rental_id=<?= htmlspecialchars($rental_id); ?>" method="POST">
                    <div class="mb-3">
                        <label for="charge_type" class="form-label">Type</label>
                        <div>
                            <input type="radio" id="penalty" name="charge_type" value="Penalty" required>
                            <label for="penalty">Penalty</label>
                        </div>
                        <div>
                            <input type="radio" id="additional_charge" name="charge_type" value="Additional Charge">
                            <label for="additional_charge">Additional Charge</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="charge" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="charge" name="charge" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <!-- Submit Button -->
                        <div class="d-flex justify-content-center" style="margin-top:10px;">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                </form>
            </section>

                <!-- Penalty Table -->
            <section class="penalty-table">
                <h2>Penalty Details</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "
                            SELECT rent_penalty_id, rent_penalty_type, rent_penalty_amount, rent_penalty_description, rent_penalty_date
                            FROM rent_penalty
                            WHERE rental_id = ? AND is_deleted = 0
                        ";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $rental_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$count}</td>
                                    <td>{$row['rent_penalty_type']}</td>
                                    <td>PHP " . number_format($row['rent_penalty_amount'], 2) . "</td>
                                    <td>{$row['rent_penalty_description']}</td>
                                    <td>{$row['rent_penalty_date']}</td>
                                    <td><a href='soft_delete_penalty.php?id={$row['rent_penalty_id']}' class='btn btn-danger'>Soft Delete</a></td>
                                </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='6'>No penalties or charges recorded.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <div class="d-flex justify-content-center"  style="margin: 10px 0;">
                    <button type="button" class="btn btn-secondary" onclick="goToApproved()">Back</button>
            </div>
             
              
         
        </div>
    </div>

    <script>
        function goToApproved() {
            window.location.href = 'index.php?content=approved_content.php';
        }
    </script>
  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>





