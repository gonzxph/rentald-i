<?php
require_once 'includes/session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Add email as username if username isn't set
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = $_SESSION['user_email'];
}

if (isset($_SESSION['pending_booking'])) {
    error_log('Session data stored successfully:');
    error_log(print_r($_SESSION['pending_booking'], true));
} else {
    error_log('Failed to store session data');
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
                                <h2 class="card-title mb-0">0</h2>
                            </div>
                        </div>
                        <a href="my-bookings.php" class="text-decoration-none">View Details</a>
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
                                <h2 class="card-title mb-0">0</h2>
                            </div>
                        </div>
                        <a href="my-bookings.php" class="text-decoration-none">View Details</a>
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
                                <h2 class="card-title mb-0">0</h2>
                            </div>
                        </div>
                        <a href="my-bookings.php" class="text-decoration-none">View History</a>
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
                        <p class="text-muted text-center py-4">No recent activity to show</p>
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