<?php
require_once 'includes/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
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

    <div class="container py-5">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h2>
                <p class="text-muted">Here's an overview of your rental activities</p>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Active Rentals</h6>
                        <h2 class="card-title">0</h2>
                        <a href="my-bookings.php" class="card-link">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Upcoming Bookings</h6>
                        <h2 class="card-title">0</h2>
                        <a href="my-bookings.php" class="card-link">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Past Rentals</h6>
                        <h2 class="card-title">0</h2>
                        <a href="my-bookings.php" class="card-link">View History</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <h4>Quick Actions</h4>
                <div class="d-flex gap-2">
                    <a href="vehicles.php" class="btn btn-primary">Rent a Car</a>
                    <a href="tours.php" class="btn btn-outline-primary">Book a Tour</a>
                    <a href="settings.php" class="btn btn-outline-secondary">Update Profile</a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <h4>Recent Activity</h4>
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted text-center">No recent activity to show</p>
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
        });
    </script>
</body>
</html>