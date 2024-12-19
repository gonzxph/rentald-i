<?php
session_start();

// Check if user came from a valid payment process
if (!isset($_SESSION['payment_reference'])) {
    header('Location: index.php');
    exit();
}

// Get the payment reference
$paymentReference = $_SESSION['payment_reference'];

// Clear the payment session variables
unset($_SESSION['payment_checkout_url']);
unset($_SESSION['payment_reference']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-container {
            text-align: center;
            padding: 50px 20px;
            max-width: 600px;
            margin: 50px auto;
        }
        .success-icon {
            color: #28a745;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .reference-number {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1 class="mb-4">Payment Successful!</h1>
            <p class="lead">Thank you for your payment. Your transaction has been completed successfully.</p>
            
            <div class="reference-number">
                <p class="mb-0">Reference Number:</p>
                <strong><?php echo htmlspecialchars($paymentReference); ?></strong>
            </div>

            <p>Please keep this reference number for your records.</p>
            
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Return to Home</a>
                <!-- Add a button to view booking details if you have such a page -->
                <a href="my_bookings.php" class="btn btn-outline-primary">View My Bookings</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</body>
</html>
