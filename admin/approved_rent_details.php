<?php
// Include necessary files
include 'db_conn.php'; 
session_start();

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
        ci.img_url,
        d.driver_name,
        d.driver_phone,
        d.driver_license_number
    FROM rental r
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    LEFT JOIN car c ON r.car_id = c.car_id
    LEFT JOIN car_image ci ON c.car_id = ci.car_id AND ci.is_primary = 1
    LEFT JOIN driver d ON r.assigned_driver_id = d.driver_id
    WHERE r.rental_id = ?
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


            
 <!-- Booking Status Section -->
 <section class="booking-status" style="margin-top: 20px;">
                <h2>Booking Status</h2>
                <p class="status-text"><?= $data['rental_status'] ?? 'N/A'; ?></p>
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
                <p>Type: <span><?= $data['is_custom_driver'] ? 'With Driver' : 'Self Drive'; ?></span></p>
            </section>

    

            <!-- Driver Information -->
            <section class="driver-information">
                <h2>Driver Information</h2>
                <p>Full Name: <span><?= $data['driver_name'] ?? 'N/A'; ?></span></p>
                <p>Contact Number: <span><?= $data['driver_phone'] ?? 'N/A'; ?></span></p>
                <p>Driver's License Number: <span><?= $data['driver_license_number'] ?? 'N/A'; ?></span></p>
            </section>

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





