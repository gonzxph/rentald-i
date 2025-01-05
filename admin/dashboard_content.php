<?php
// Include database connection
include 'db_conn.php';

// Query to count total active clients (users with user_role 'USER' and user_status 'ACTIVE')
$clientQuery = "SELECT COUNT(*) AS total_clients FROM user WHERE user_role = 'USER' AND user_status = 'ACTIVE'";
$clientResult = $conn->query($clientQuery);
$clientData = $clientResult->fetch_assoc();
$totalClients = $clientData['total_clients'];

// Query to count total bookings (rentals with rental_status 'APPROVED')
$bookingQuery = "SELECT COUNT(*) AS total_bookings FROM rental WHERE rent_status = 'APPROVED'";
$bookingResult = $conn->query($bookingQuery);
$bookingData = $bookingResult->fetch_assoc();
$totalBookings = $bookingData['total_bookings'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_content.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container-fluid">
    <div class="outer-box ">
        <h1 class="text-left mb-4">Dashboard</h1>
        <div class="row g-4">
            <!-- Total Clients -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="client-box">
                    <h2>Total Clients</h2>
                    <p><?php echo $totalClients; ?></p>    
                </div>
            </div>
            <!-- Total Bookings -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="client-box">
                    <h2>Total Bookings</h2>
                    <p><?php echo $totalBookings; ?></p>    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
