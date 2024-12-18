<?php
session_start();
require_once 'config/db.php';

// Get the reference number from URL parameter
$referenceNumber = $_GET['reference'] ?? null;

if (!$referenceNumber) {
    header('Location: dashboard.php');
    exit;
}

// Verify the payment status
$stmt = $db->prepare("SELECT * FROM payment WHERE payment_reference = ? AND pay_status = 'completed'");
$stmt->execute([$referenceNumber]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    // Payment not found or not completed
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <!-- Add your CSS here -->
</head>
<body>
    <div class="success-container">
        <h1>Payment Successful!</h1>
        <p>Your payment reference number: <?php echo htmlspecialchars($referenceNumber); ?></p>
        <p>Thank you for your payment. Your rental has been confirmed.</p>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>