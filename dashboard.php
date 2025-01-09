<?php
require_once 'includes/session.php';
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Add email as username if username isn't set
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = $_SESSION['user_email'];
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

try {
    // Get Active Rentals Count - Include only APPROVED rentals that are currently ongoing
    $activeQuery = "SELECT COUNT(*) FROM rental 
                   WHERE user_id = :user_id 
                   AND rent_status = 'APPROVED' 
                   AND rent_pickup_datetime <= CURRENT_TIMESTAMP 
                   AND rent_dropoff_datetime > CURRENT_TIMESTAMP";
    $activeStmt = $db->prepare($activeQuery);
    $activeStmt->execute(['user_id' => $user_id]);
    $activeCount = $activeStmt->fetchColumn();

    // Get Upcoming Bookings Count - Include both PENDING and APPROVED future rentals
    $upcomingQuery = "SELECT COUNT(*) FROM rental 
                     WHERE user_id = :user_id 
                     AND rent_status IN ('APPROVED', 'PENDING')
                     AND rent_pickup_datetime > CURRENT_TIMESTAMP";
    $upcomingStmt = $db->prepare($upcomingQuery);
    $upcomingStmt->execute(['user_id' => $user_id]);
    $upcomingCount = $upcomingStmt->fetchColumn();

    // Get Past Rentals Count - Include COMPLETED rentals
    $pastQuery = "SELECT COUNT(*) FROM rental 
                 WHERE user_id = :user_id 
                 AND rent_status = 'COMPLETED'";
    $pastStmt = $db->prepare($pastQuery);
    $pastStmt->execute(['user_id' => $user_id]);
    $pastCount = $pastStmt->fetchColumn();

    // Get Recent Activity
    $recentQuery = "SELECT r.*, c.CAR_BRAND, c.CAR_MODEL 
                   FROM rental r
                   JOIN CAR c ON r.car_id = c.CAR_ID
                   WHERE r.user_id = :user_id 
                   ORDER BY r.rent_pickup_datetime DESC 
                   LIMIT 5";
    $recentStmt = $db->prepare($recentQuery);
    $recentStmt->execute(['user_id' => $user_id]);
    $recentActivities = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    // Set default values in case of error
    $activeCount = 0;
    $upcomingCount = 0;
    $pastCount = 0;
    $recentActivities = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | <?php echo htmlspecialchars($_SESSION['username']); ?></title>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Success message display -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex justify-content-center" role="alert">
            <?php 
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="container py-5" style="min-height: calc(100vh - 56px);">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h2>
                <p class="text-muted mb-0">Here's an overview of your rental activities</p>
            </div>
        </div>

        <!-- Quick Stats Cards - Updated Design -->
        <div class="row mb-5 g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-light p-3 me-3">
                                <i class="fas fa-car text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Active Rentals</h6>
                                <h2 class="card-title mb-0"><?php echo $activeCount; ?></h2>
                            </div>
                        </div>
                        <a href="my-bookings.php?filter=active" class="text-decoration-none">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-light p-3 me-3">
                                <i class="fas fa-calendar text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Upcoming Bookings</h6>
                                <h2 class="card-title mb-0"><?php echo $upcomingCount; ?></h2>
                            </div>
                        </div>
                        <a href="my-bookings.php?filter=upcoming" class="text-decoration-none">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-light p-3 me-3">
                                <i class="fas fa-history text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle text-muted mb-1">Past Rentals</h6>
                                <h2 class="card-title mb-0"><?php echo $pastCount; ?></h2>
                            </div>
                        </div>
                        <a href="my-bookings.php?filter=past" class="text-decoration-none">View History</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions - Updated Design -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Quick Actions</h4>
                        <p class="text-muted small mb-4">Get started with your rental journey</p>
                        <div class="d-flex gap-3">
                            <a href="index.php" class="btn btn-dark px-4">
                                <i class="fas fa-car me-2"></i>Rent a Car
                            </a>
                            <a href="tours.php" class="btn btn-outline-dark px-4">
                                <i class="fas fa-map-marked-alt me-2"></i>Book a Tour
                            </a>
                            <a href="settings.php" class="btn btn-outline-dark px-4">
                                <i class="fas fa-user me-2"></i>Update Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity - Updated Design -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Recent Activity</h4>
                        <p class="text-muted small mb-4">Your latest rental activities</p>
                        <?php if (empty($recentActivities)): ?>
                            <p class="text-muted text-center py-4">No recent activity to show</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Vehicle</th>
                                            <th>Pickup Date</th>
                                            <th>Return Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentActivities as $activity): ?>
                                            <tr class="rental-row" style="cursor: pointer;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rentalModal"
                                                data-rental='<?php echo json_encode($activity); ?>'>
                                                <td><?php echo htmlspecialchars($activity['CAR_BRAND'] . ' ' . $activity['CAR_MODEL']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($activity['rent_pickup_datetime'])); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($activity['rent_dropoff_datetime'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($activity['rent_status']) {
                                                            'APPROVED' => 'success',
                                                            'PENDING' => 'warning',
                                                            'REJECTED' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo htmlspecialchars($activity['rent_status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);

            // Handle rental row clicks
            $('.rental-row').on('click', function() {
                const rentalData = $(this).data('rental');
                
                // Format dates
                const pickupDate = new Date(rentalData.rent_pickup_datetime);
                const returnDate = new Date(rentalData.rent_dropoff_datetime);
                
                // Update modal content
                $('#modalVehicle').text(rentalData.CAR_BRAND + ' ' + rentalData.CAR_MODEL);
                $('#modalPickup').text(pickupDate.toLocaleString());
                $('#modalReturn').text(returnDate.toLocaleString());
                $('#modalCost').text('$' + parseFloat(rentalData.rent_total_cost).toFixed(2));
                
                // Set status with badge
                const statusColors = {
                    'APPROVED': 'success',
                    'PENDING': 'warning',
                    'REJECTED': 'danger',
                    'COMPLETED': 'secondary'
                };
                const badgeColor = statusColors[rentalData.rent_status] || 'secondary';
                $('#modalStatus').html(`<span class="badge bg-${badgeColor}">${rentalData.rent_status}</span>`);
            });
        });
    </script>

    <!-- Rental Details Modal -->
    <div class="modal fade" id="rentalModal" tabindex="-1" aria-labelledby="rentalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rentalModalLabel">Rental Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <h4 id="modalVehicle" class="mb-3"></h4>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Pickup Date & Time</p>
                            <p id="modalPickup" class="fw-bold"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Return Date & Time</p>
                            <p id="modalReturn" class="fw-bold"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Status</p>
                            <p id="modalStatus"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Total Cost</p>
                            <p id="modalCost" class="fw-bold"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>