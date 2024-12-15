// payment_success.php
<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['booking_details'])) {
    $bookingDetails = $_SESSION['booking_details'];
    
    // Here you can:
    // 1. Save the booking to database
    // 2. Send confirmation email
    // 3. Clear the session
    // 4. Show success message
    
    unset($_SESSION['booking_details']);
}