<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <title>Pseudo SPA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="logout.php">Logout</a>
        </div>
    </nav>

    <!-- Rest of your existing index.php content -->
</body>
</html>