<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_POST['payment_reference'])) {
    echo json_encode(['error' => 'No payment reference provided']);
    error_log(json_encode(['error' => 'No payment reference provided']));
    exit();
}

$reference = $_POST['payment_reference'];

// First check if payment exists in the main payment table
$query = "SELECT pay_status FROM payment WHERE payment_reference = ?";
$stmt = $db->prepare($query);
$stmt->execute([$reference]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);


if ($result) {
    // Payment found in main payment table
    echo json_encode(['status' => 'completed']);
} else {
    // Check if it still exists in pending_payments
    $query = "SELECT 1 FROM pending_payments WHERE payment_reference = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$reference]);
    $pending = $stmt->fetch();

    if ($pending) {
        // Payment is still pending
        echo json_encode(['status' => 'pending']);
    } else {
        // Payment reference not found in either table
        echo json_encode(['status' => 'failed']);
    }
}