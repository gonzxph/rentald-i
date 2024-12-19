<?php
require_once 'includes/session.php';  // New centralized session management

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();

    if (isset($_SESSION['pending_booking'])) {
        error_log('Session data stored successfully:');
        error_log(print_r($_SESSION['pending_booking'], true));
    } else {
        error_log('Failed to store session data');
    }


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

    <!-- Add the success message display -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            <?php 
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']); // Clear the message after displaying
            ?>
        </div>
    <?php endif; ?>

    <!-- Rest of your existing index.php content -->
    
</body>
<?php include 'includes/scripts.php' ?>
<script>
    // Auto-hide success alerts after 3 seconds using jQuery
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-success').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    });
</script>
</html>