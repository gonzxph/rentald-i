<?php
require_once 'includes/session.php';
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

try {
    // Base query with corrected column names
    $query = "SELECT r.*, c.car_brand, c.car_model, c.car_rental_rate 
              FROM rental r
              JOIN car c ON r.car_id = c.car_id
              WHERE r.user_id = :user_id";
    
    // Add filter conditions
    switch ($filter) {
        case 'active':
            $query .= " AND r.rent_status = 'APPROVED' 
                       AND r.rent_pickup_datetime <= CURRENT_TIMESTAMP 
                       AND r.rent_dropoff_datetime > CURRENT_TIMESTAMP";
            break;
        case 'upcoming':
            $query .= " AND r.rent_status IN ('APPROVED', 'PENDING')
                       AND r.rent_pickup_datetime > CURRENT_TIMESTAMP";
            break;
        case 'completed':
        case 'past':
            $query .= " AND r.rent_status = 'COMPLETED'";
            break;
        case 'cancelled':
            $query .= " AND r.rent_status = 'CANCELLED'";
            break;
    }
    
    $query .= " ORDER BY r.rent_pickup_datetime DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching bookings: " . $e->getMessage());
    $bookings = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings | <?php echo htmlspecialchars($_SESSION['username']); ?></title>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-1">My Bookings</h2>
                <p class="text-muted">Manage all your car rental bookings</p>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <a href="?filter=all" class="btn btn-<?php echo $filter === 'all' ? 'dark' : 'outline-dark'; ?>">
                        All Bookings
                    </a>
                    <a href="?filter=active" class="btn btn-<?php echo $filter === 'active' ? 'dark' : 'outline-dark'; ?>">
                        Active
                    </a>
                    <a href="?filter=upcoming" class="btn btn-<?php echo $filter === 'upcoming' ? 'dark' : 'outline-dark'; ?>">
                        Upcoming
                    </a>
                    <a href="?filter=completed" class="btn btn-<?php echo ($filter === 'completed' || $filter === 'past') ? 'dark' : 'outline-dark'; ?>">
                        Completed
                    </a>
                    <a href="?filter=cancelled" class="btn btn-<?php echo $filter === 'cancelled' ? 'dark' : 'outline-dark'; ?>">
                        Cancelled
                    </a>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($bookings)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No bookings found</h5>
                            <p class="text-muted">You don't have any <?php echo $filter; ?> bookings.</p>
                            <a href="index.php" class="btn btn-dark">Browse Cars</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Vehicle</th>
                                        <th>Pickup Date</th>
                                        <th>Return Date</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($booking['rental_id']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['car_brand'] . ' ' . $booking['car_model']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($booking['rent_pickup_datetime'])); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($booking['rent_dropoff_datetime'])); ?></td>
                                            <td>PHP <?php echo number_format($booking['rent_total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($booking['rent_status']) {
                                                        'APPROVED' => 'success',
                                                        'PENDING' => 'warning',
                                                        'COMPLETED' => 'info',
                                                        'CANCELLED' => 'danger',
                                                        'REJECTED' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo htmlspecialchars($booking['rent_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="view-booking.php?id=<?php echo $booking['rental_id']; ?>" 
                                                       class="btn btn-sm btn-outline-dark">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($booking['rent_status'] === 'PENDING'): ?>
                                                        <a href="cancel-booking.php?id=<?php echo $booking['rental_id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>
</html> 