<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Debug 1: Starting payment process");
        
        $carId = $_POST['carId'];
        $paymentOption = $_POST['paymentOption'];
        $pickupAddress = $_POST['pickupAddress'];
        $pickupDate = $_POST['pickupDate'];
        $returnAddress = $_POST['returnAddress'];
        $returnDate = $_POST['returnDate'];
        $vehicleRate = floatval($_POST['vehicleRate']);
        $excessPay = floatval($_POST['excessPay']);
        $deliveryFee = floatval($_POST['DeliveryFeeInput']);
        $returnFee = floatval($_POST['PickupFeeInput']);

        // Convert dates from "December 29, 2024 at 04:00 AM" to "2024-12-29 04:00:00"
        $pickupDate = DateTime::createFromFormat('F j, Y \a\t h:i A', $_POST['pickupDate']);
        $returnDate = DateTime::createFromFormat('F j, Y \a\t h:i A', $_POST['returnDate']);
        
        // Format dates for database storage
        $pickupDate = $pickupDate->format('Y-m-d H:i:s');
        $returnDate = $returnDate->format('Y-m-d H:i:s');

        // Recalculate total rental fee
        $totalRentalFee = $vehicleRate + $excessPay + $deliveryFee + $returnFee;
        
        // Calculate payment amount based on option
        $paymentAmount = ($paymentOption === 'reservation') ? 500 : $totalRentalFee;
        
        // Before DB transaction
        error_log("Debug 3: Starting DB transaction");
        $db->beginTransaction();

        // Insert into pending_payments table
        $pendingPaymentQuery = "INSERT INTO pending_payments (
            car_id,
            user_id,
            payment_option,
            pickup_address,
            pickup_date,
            return_address,
            return_date,
            vehicle_rate,
            delivery_fee,
            return_fee,
            total_amount,
            amount_paid,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $db->prepare($pendingPaymentQuery);
        $stmt->execute([
            $carId,
            42,
            $paymentOption,
            $pickupAddress,
            $pickupDate,
            $returnAddress,
            $returnDate,
            $vehicleRate,
            $deliveryFee,
            $returnFee,
            $totalRentalFee,
            $paymentAmount
        ]);

        error_log("Debug 4: Inserted into pending_payments");
        
        // Create PayMongo payment link
        $secretKey = "sk_test_6XrwLnwC4nHDCf4CuTLxDcKq";
        $amount = $paymentAmount * 100;
        
        $description = $paymentOption === 'reservation' 
            ? "Reservation Fee for Car Rental" 
            : "Full Payment for Car Rental";

        $baseUrl = "https://b757-2001-4454-1bc-500-1a3-ec25-583b-2a22.ngrok-free.app/rental";
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount,
                    'description' => $description,
                    'remarks' => 'Car Rental Payment',
                    'success_url' => $baseUrl . "/payment_success.php",
                    'cancel_url' => $baseUrl . "/payment_failed.php"
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.paymongo.com/v1/links",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Basic " . base64_encode($secretKey . ":"),
                "Content-Type: application/json"
            ]
        ]);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            error_log("Debug ERROR: CURL Error - " . $err);
            throw new Exception("Payment gateway error");
        }

        $response = json_decode($result, true);

        if (isset($response['data']['attributes']['checkout_url'])) {
            error_log("Debug 7: Got checkout URL, updating payment reference");
            // Update pending_payment with payment reference
            $paymentReference = $response['data']['attributes']['reference_number'];
            $updateQuery = "UPDATE pending_payments 
                          SET payment_reference = ? 
                          WHERE car_id = ? AND user_id = ? 
                          ORDER BY id DESC LIMIT 1";
            
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([$paymentReference, $carId, 42]);
            
            $db->commit();

            // Add debug logging
            error_log("Final PayMongo Response: " . $result);

            header("Location: " . $response['data']['attributes']['checkout_url']);
            exit();
        } else {
            error_log("Debug 8: ERROR - No checkout URL in response");
            throw new Exception("Error creating payment link");
        }

    } catch (Exception $e) {
        error_log("Debug ERROR: " . $e->getMessage());
        error_log("Debug ERROR Stack Trace: " . $e->getTraceAsString());
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../booking.php?carid=' . $carId);
        exit();
    }
}
?>