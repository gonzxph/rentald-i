<?php
// Include necessary files
include 'db_conn.php'; 
session_start();

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role'])) {
    header('Location: ../signin.php');
    exit();
}

// Check if user is neither ADMIN nor AGENT
if ($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'AGENT') {
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
        r.rent_rejected_datetime,
        u.user_fname,
        u.user_mname,
        u.user_lname,
        u.user_email,
        u.user_phone,
        c.car_brand,
        c.car_model,
        c.car_year,
        c.car_type,
        c.car_color,
        c.car_seats,
        c.car_transmission_type,
        c.car_fuel_type,
        p.pay_type,
        p.pay_total_due,
        p.pay_amount_paid,
        p.pay_balance_due
    FROM rental r
    LEFT JOIN user u ON r.user_id = u.user_id
    LEFT JOIN car c ON r.car_id = c.car_id
    LEFT JOIN payment p ON r.rental_id = p.rental_id
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
    <link rel="stylesheet" href="approved_rent_details.css">
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

            <!-- Rejected Date & Time -->
            <section class="booking-status" style="margin-top: 20px;">
                <h2>The request was rejected on</h2>
                <p>Date: <span><?= isset($data['rent_rejected_datetime']) ? date('F d, Y', strtotime($data['rent_rejected_datetime'])) : 'N/A'; ?></span></p>
                <p>Time: <span><?= isset($data['rent_rejected_datetime']) ? date('h:i A', strtotime($data['rent_rejected_datetime'])) : 'N/A'; ?></span></p>
            </section>

            <!-- Renter Info -->
            <section class="renter-info" style="margin-top: 20px;">
                <h2>Renter Information</h2>
                <p>Full Name: <span>
                    <?php
                    $full_name = trim(
                        ($data['user_fname'] ?? '') . ' ' .
                        ($data['user_mname'] ?? '') . ' ' .
                        ($data['user_lname'] ?? '')
                    );
                    echo $full_name ?: 'N/A';
                    ?>
                </span></p>
                <p>Email: <span><?= $data['user_email'] ?? 'N/A'; ?></span></p>
                <p>Phone Number: <span><?= $data['user_phone'] ?? 'N/A'; ?></span></p>
            </section>

            <!-- Chosen Car -->
            <section class="choosen-car">
                <h2>Chosen Car</h2>
                <p>Vehicle: <span><?= isset($data['car_brand'], $data['car_model']) ? $data['car_brand'] . ' ' . $data['car_model'] : 'N/A'; ?></span></p>
                <p>Year: <span><?= $data['car_year'] ?? 'N/A'; ?></span></p>
                <p>Type: <span><?= $data['car_type'] ?? 'N/A'; ?></span></p>
                <p>Color: <span><?= $data['car_color'] ?? 'N/A'; ?></span></p>
                <p>Seats: <span><?= $data['car_seats'] ?? 'N/A'; ?></span></p>
                <p>Transmission Type: <span><?= $data['car_transmission_type'] ?? 'N/A'; ?></span></p>
                <p>Fuel Type: <span><?= $data['car_fuel_type'] ?? 'N/A'; ?></span></p>
            </section>

            <!-- Payment Information -->
            <section class="payment-info">
                <h2>Payment Information</h2>
                <p>Payment Method: <span><?= $data['pay_type'] ?? 'N/A'; ?></span></p>
                <p>Total Amount Due: <span>PHP <?= number_format($data['pay_total_due'], 2) ?? '0.00'; ?></span></p>
                <p>Amount Paid: <span>PHP <?= number_format($data['pay_amount_paid'], 2) ?? '0.00'; ?></span></p>
                <p>Balance Due: <span>PHP <?= number_format($data['pay_balance_due'], 2) ?? '0.00'; ?></span></p>
            </section>

            <div class="d-flex justify-content-center" style="margin: 10px 0;">
                <button type="button" class="btn btn-secondary" onclick="goBack()">Back</button>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = 'index.php?content=approved_content.php';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
