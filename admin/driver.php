<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role'])) {
    header('Location: ../signin.php');
    exit();
}

// Check if user is DRIVER
if ($_SESSION['user_role'] !== 'DRIVER') {
    header('Location: ../signin.php');
    exit();
}

$role = $_SESSION['user_role'];
$id = $_SESSION['user_id'];

include 'db_conn.php'; // Include your DB connection file

$rentalDetails = null;

if (isset($_GET['rental_id'])) {
    $rental_id = intval($_GET['rental_id']);
    $query = "
        SELECT 
            r.rental_id,
            CONCAT(u.user_fname, ' ', IFNULL(u.user_mname, ''), ' ', u.user_lname) AS user_name,
            r.car_id,
            r.is_custom_driver,
            r.custom_driver_name,
            r.custom_driver_phone,
            r.custom_driver_license_number,
            r.rent_pickup_datetime,
            r.rent_pickup_location,
            r.rent_dropoff_datetime,
            r.rent_dropoff_location,
            r.rent_status,
            r.rent_total_price
            
        FROM rental r
        LEFT JOIN user u ON r.user_id = u.user_id
        WHERE r.rental_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rentalDetails = $result->fetch_assoc();
    }
    $stmt->close();
}

// Query to fetch approved rentals assigned to the logged-in driver
$query = "
    SELECT 
        r.rental_id,
        CONCAT(u.user_fname, ' ', IFNULL(u.user_mname, ''), ' ', u.user_lname) AS user_name,
        r.rent_pickup_datetime,
        r.rent_dropoff_datetime,
        r.car_id,
        r.rent_pickup_location,
        r.rent_dropoff_location,
        r.rent_total_price,
        r.rent_status
    FROM rental r
    JOIN user u ON r.user_id = u.user_id
    WHERE r.assigned_driver_id = ? 
      AND r.rent_status = 'APPROVED'
      AND DATE(r.rent_pickup_datetime) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
    ORDER BY r.rent_pickup_datetime DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';  
    ?>
    <title>D&I CEBU CAR RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Approved Rentals</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Rental ID</th>
                    <th>Customer Name</th>
                    <th>Pickup DateTime</th>
                    <th>Dropoff DateTime</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['rental_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['rent_pickup_datetime']) ?></td>
                        <td><?= htmlspecialchars($row['rent_dropoff_datetime']) ?></td>
                        <td>
                            <!-- Button inside the table -->
                            <form method="GET" action="">
                                <input type="hidden" name="rental_id" value="<?= $row['rental_id']; ?>">
                                <button type="submit" class="btn btn-primary">View More</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($rentalDetails): ?>
        <div class="modal" id="rentalDetailsModal" tabindex="-1" aria-hidden="true" style="display: block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rental Details</h5>
                        <button type="button" class="btn-close" onclick="location.href='driver.php'"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Rental ID:</strong> <?= htmlspecialchars($rentalDetails['rental_id']); ?></p>
                        <p><strong>Customer Name:</strong> <?= htmlspecialchars($rentalDetails['user_name']); ?></p>
                        <p><strong>Pickup Date & Time:</strong> <?= htmlspecialchars($rentalDetails['rent_pickup_datetime']); ?></p>
                        <p><strong>Dropoff Date & Time:</strong> <?= htmlspecialchars($rentalDetails['rent_dropoff_datetime']); ?></p>
                        <p><strong>Pickup Location:</strong> <?= htmlspecialchars($rentalDetails['rent_pickup_location']); ?></p>
                        <p><strong>Dropoff Location:</strong> <?= htmlspecialchars($rentalDetails['rent_dropoff_location']); ?></p>
                        <p><strong>Total Price:</strong> <?= htmlspecialchars($rentalDetails['rent_total_price']); ?></p>
                        
                        <?php if ($rentalDetails['is_custom_driver']): ?>
                            <p><strong>Custom Driver Name:</strong> <?= htmlspecialchars($rentalDetails['custom_driver_name']); ?></p>
                            <p><strong>Custom Driver Phone:</strong> <?= htmlspecialchars($rentalDetails['custom_driver_phone']); ?></p>
                            <p><strong>Driver License Number:</strong> <?= htmlspecialchars($rentalDetails['custom_driver_license_number']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="location.href='driver.php'">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>
