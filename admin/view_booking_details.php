<?php
// Include necessary files
include 'db_conn.php'; 
session_start();

// Check for rental_id in the query parameter
$rental_id = $_GET['rental_id'];
if (empty($rental_id)) {
    header("Location: booking_content.php?error=invalid_id");
    exit();
}

// Fetch the rental details as before, including the driver's image for self-drive bookings
$sql = "
    SELECT 
        r.rent_status,
        r.rent_pickup_datetime,
        r.rent_pickup_location,
        r.rent_dropoff_datetime,
        r.rent_dropoff_location,
        r.is_custom_driver,
        r.custom_driver_name,
        r.custom_driver_phone,
        r.custom_driver_license_number,
        u.user_fname,
        u.user_mname,
        u.user_lname,
        u.user_email,
        u.user_phone,
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
        di.dimg_path  
    FROM rental r
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    LEFT JOIN car c ON r.car_id = c.car_id
    LEFT JOIN car_image ci ON c.car_id = ci.car_id AND ci.is_primary = 1
    LEFT JOIN driver_id_image di ON r.rental_id = di.rental_id
    LEFT JOIN user u ON r.user_id = u.user_id
    WHERE r.rental_id = ?
";







$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
    <title>D&I CEBU CAR RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="view_booking_details.css">
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
            <section class="booking-status">
                <h2>Booking Status</h2>
                <p class="status-text"><?= $data['rent_status'] ?? 'N/A'; ?></p>
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
                <p>Full Name: <span><?= $data['custom_driver_name'] ?? 'N/A'; ?></span></p>
                <p>Contact Number: <span><?= $data['custom_driver_phone'] ?? 'N/A'; ?></span></p>
                <p>Driver's License Number: <span><?= $data['custom_driver_license_number'] ?? 'N/A'; ?></span></p>

                <?php if (!empty($data['dimg_path'])): ?>
                    <!-- Small fixed size image with modal functionality -->
                    <p>Driver's License Image: 
                        <img src="../upload/driver_ids/<?= htmlspecialchars($data['dimg_path']); ?>" 
                            alt="Driver Image" 
                            width="100" 
                            height="100" 
                            style="cursor: pointer;" 
                            data-bs-toggle="modal" 
                            data-bs-target="#driverImageModal">
                    </p>

                    <!-- Modal for larger image view -->
                    <div class="modal fade" id="driverImageModal" tabindex="-1" aria-labelledby="driverImageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="driverImageModalLabel">Driver's License Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="../upload/driver_ids/<?= htmlspecialchars($data['dimg_path']); ?>" 
                                        alt="Driver Image" 
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>



            <!-- Price Breakdown -->
            <section class="price-breakdown">
                <h2>Price Breakdown</h2>
                <p>Rental Charge: <span>PHP <?= number_format($data['pay_rental_charge'], 2) ?? '0.00'; ?></span></p>
                <p>Total Amount Due: <span>PHP <?= number_format($data['pay_total_due'], 2) ?? '0.00'; ?></span></p>
                <p>Amount Paid: <span>PHP <?= number_format($data['pay_amount_paid'], 2) ?? '0.00'; ?></span></p>
                <p>Balance Due: <span>PHP <?= number_format($data['pay_balance_due'], 2) ?? '0.00'; ?></span></p>
            </section>

            <section class="action-buttons">
                <!-- Back Button -->
                <button class="btn btn-secondary back-button" onclick="goToBookingContent()">Back</button>

                <!-- Decline and Approve Buttons -->
                <button class="btn btn-danger decline-button" onclick="rejectRental(<?= $rental_id; ?>)">Reject</button>
                <button class="btn btn-primary approve-button" onclick="approveRental(<?= $rental_id; ?>)">Approve</button>
            </section>
        </div>
    </div>

    <script>
        function goToBookingContent() {
            window.location.href = 'index.php?content=booking_content.php';
        }

        function approveRental(rentalId) {
            fetch('approve_rental.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rental_id: rentalId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'index.php?content=booking_content.php'; // Reload the page to reflect changes
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function rejectRental(rentalId) {
            fetch('reject_rental.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rental_id: rentalId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'index.php?content=booking_content.php'; // Reload the page to reflect changes
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
