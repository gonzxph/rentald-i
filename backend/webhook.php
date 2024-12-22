// backend/webhook_handler.php
<?php
require_once '../config/db.php';
session_start();
// PayMongo webhook secret key
$webhookSecret = 'whsk_UT6owvZDHn1FiCpSU9KQthqL';

// Add these validation functions at the top
function computeHmacSignature($payload, $webhookSecret, $timestamp) {
    $signedPayload = $timestamp . '.' . $payload;
    return hash_hmac('sha256', $signedPayload, $webhookSecret);
}

function isValidPayMongoRequest($payload, $sigHeader, $webhookSecret) {
    // Parse the signature header
    $components = explode(',', $sigHeader);
    $signatures = [];
    
    foreach ($components as $component) {
        $parts = explode('=', $component);
        if (count($parts) === 2) {
            $signatures[$parts[0]] = $parts[1];
        }
    }
    
    if (!isset($signatures['t']) || !isset($signatures['te'])) {
        return false;
    }
    
    $timestamp = $signatures['t'];
    $expectedSignature = $signatures['te'];
    
    // Compute our signature
    $computedSignature = computeHmacSignature($payload, $webhookSecret, $timestamp);
    
    // Compare signatures
    return hash_equals($expectedSignature, $computedSignature);
}

// Get webhook payload and signature
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Debug logging
error_log("Raw Payload Received: " . $payload);
error_log("All Headers: " . print_r($headers, true));

// Replace the webhook processing code
$sigHeader = $headers['Paymongo-Signature'] ?? '';

// Validate the signature
if (!isValidPayMongoRequest($payload, $sigHeader, $webhookSecret)) {
    error_log("Invalid webhook signature");
    http_response_code(401);
    exit;
}

// Parse the webhook payload
$event = json_decode($payload, true);

// Add error checking for JSON decode and event structure
if ($event === null) {
    error_log("Failed to decode JSON payload");
    http_response_code(400);
    exit;
}

// Check for the nested type attribute
if (!isset($event['data']['attributes']['type'])) {
    error_log("Missing 'type' in webhook payload: " . print_r($event, true));
    http_response_code(400);
    exit;
}

// Use the correct path to access the type
$eventType = $event['data']['attributes']['type'];
error_log("Event Type Received: " . $eventType);

// Add more specific event type handling
if ($eventType === 'payment.paid') {
    error_log("Processing successful payment");
    // ... existing success code ...
} elseif ($eventType === 'payment.failed' || $eventType === 'payment.expired') {
    error_log("Processing failed/expired payment");
    // ... existing failure code ...
} else {
    error_log("Unhandled event type: " . $eventType);
}

try {
    // Get the payment reference number from the event
    $referenceNumber = $event['data']['attributes']['data']['attributes']['external_reference_number'] ?? null;

    if (!$referenceNumber) {
        throw new Exception('Reference number not found in webhook data');
    }

    error_log("Processing reference number: " . $referenceNumber);
    
    $db->beginTransaction();
    
    if ($eventType === 'payment.paid') {
        // Log the entire webhook data for debugging
        error_log("Full webhook data: " . print_r($event, true));

        // Get pending payment using reference number
        $stmt = $db->prepare("SELECT * FROM pending_payments WHERE payment_reference = ?");
        $stmt->execute([$referenceNumber]);
        $pendingPayment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pendingPayment) {
            throw new Exception('No pending payment found for reference: ' . $referenceNumber);
        }

        // Insert rental record with driver information
        $rentalQuery = "INSERT INTO rental (
            car_id,
            user_id,
            is_custom_driver,
            custom_driver_name,
            custom_driver_phone,
            custom_driver_license_number,
            rent_pickup_datetime,
            RENT_PICKUP_LOCATION,
            rent_dropoff_datetime,
            RENT_DROPOFF_LOCATION,
            RENT_TOTAL_PRICE
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($rentalQuery);
        $stmt->execute([
            $pendingPayment['car_id'],
            $pendingPayment['user_id'],
            $pendingPayment['is_custom_driver'],
            $pendingPayment['custom_driver_name'],
            $pendingPayment['custom_driver_phone'],
            $pendingPayment['custom_driver_license_number'],
            $pendingPayment['pickup_date'],
            $pendingPayment['pickup_address'],
            $pendingPayment['return_date'],
            $pendingPayment['return_address'],
            $pendingPayment['total_amount']
        ]);

        $rentalId = $db->lastInsertId();

        // After rental record is created and we have $rentalId
        // Handle the driver ID images if they exist in the session
        if ($pendingPayment['is_custom_driver']) {
            $tempUploadDir = '../upload/temp/';
            $finalUploadDir = '../upload/driver_ids/';
            
            // Create final directory if it doesn't exist
            if (!file_exists($finalUploadDir)) {
                mkdir($finalUploadDir, 0777, true);
            }

            // Get temporary image filenames from pending_payment record instead of session
            $tempImages = json_decode($pendingPayment['temp_driver_images'] ?? '[]', true);
            
            if (!empty($tempImages)) {
                $imageQuery = "INSERT INTO driver_id_images (rental_id, dimg_path) VALUES (?, ?)";
                $stmt = $db->prepare($imageQuery);
                
                foreach ($tempImages as $tempFileName) {
                    $tempPath = $tempUploadDir . $tempFileName;
                    $finalPath = $finalUploadDir . $tempFileName;
                    
                    // Move file from temp to final location
                    if (file_exists($tempPath) && rename($tempPath, $finalPath)) {
                        // Save record to database
                        $stmt->execute([$rentalId, $tempFileName]);
                    }
                }
            }
        }

        // Insert payment record
        $paymentQuery = "INSERT INTO payment (
            rental_id,
            pay_type,
            pay_date,
            pay_rental_charge,
            pay_pickup_charge,
            pay_dropoff_charge,
            pay_reservation_fee,
            pay_total_due,
            pay_amount_paid,
            pay_balance_due,
            pay_status,
            payment_reference
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($paymentQuery);
        $stmt->execute([
            $rentalId,
            $pendingPayment['payment_option'],
            date('Y-m-d'),
            $pendingPayment['vehicle_rate'],
            $pendingPayment['delivery_fee'],
            $pendingPayment['return_fee'],
            ($pendingPayment['payment_option'] === 'reservation' ? 500 : 0),
            $pendingPayment['total_amount'],
            $pendingPayment['amount_paid'],
            ($pendingPayment['total_amount'] - $pendingPayment['amount_paid']),
            'completed',
            $pendingPayment['payment_reference']
        ]);

        // Delete the pending payment record
        $stmt = $db->prepare("DELETE FROM pending_payments WHERE id = ?");
        $stmt->execute([$pendingPayment['id']]);

        error_log("Payment and rental created successfully");
        http_response_code(200);
    } else {
        // Handle failed/expired payment using reference number
        $stmt = $db->prepare("DELETE FROM pending_payments WHERE payment_reference = ?");
        $stmt->execute([$referenceNumber]);
        
        error_log("Failed payment - pending payment deleted for reference: " . $referenceNumber);
        
    }
    
    $db->commit();
    http_response_code(200);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error processing webhook: " . $e->getMessage());
    http_response_code(500);
}