<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="admin_dashboard_pics/logo.png" alt="D&I Cebu Car Rental Logo">
        </div>

        <div class="login-section">
            <h2>Admin Login</h2>
            <form action="../backend/login_handler.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <a href="#" class="forgot-password">Forgot password?</a>
                <button type="submit" class="login-button">Login</button>
            </form>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
