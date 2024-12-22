<?php
session_start();

if (!isset($_SESSION['payment_checkout_url'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Processing Payment</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="payment-iframe-container">
        <iframe src="<?php echo $_SESSION['payment_checkout_url']; ?>" 
                style="width: 100%; height: 800px; border: none;">
        </iframe>
    </div>

    <script>
    // Poll for payment status every 5 seconds
    function checkPaymentStatus() {
        $.ajax({
            url: '../rental/backend/check_payment_status.php',
            method: 'POST',
            data: {
                payment_reference: '<?php echo $_SESSION['payment_reference']; ?>'
            },
            success: function(response) {
                if (response.status === 'completed') {
                    // Redirect to success page
                    window.location.href = 'payment_success.php';
                } else if (response.status === 'failed') {
                    // Redirect to failed page
                    window.location.href = 'payment_failed.php';
                }
                // If still pending, continue polling
            }
        });
    }

    // Start polling
    setInterval(checkPaymentStatus, 5000);
    </script>
</body>
</html>