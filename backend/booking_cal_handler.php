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

        // Recalculate total rental fee
        $totalRentalFee = $vehicleRate + $excessPay + $deliveryFee + $returnFee;
        
        // Calculate payment amount based on option
        $paymentAmount = ($paymentOption === 'reservation') ? 500 : $totalRentalFee;

        error_log("Debug 2: POST data received - carId: $carId, paymentOption: $paymentOption");
        
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

        // Prepare PayMongo data with success_url and cancel_url
        $data = [
            "data" => [
                "attributes" => [
                    "amount" => $amount,
                    "currency" => "PHP",
                    "description" => $description
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($secretKey . ":")
        ]);
        

        $result = curl_exec($ch);
        curl_close($ch);

        error_log("Debug 5: Preparing PayMongo API call - Amount: $amount");
        
        error_log("Debug 6: PayMongo API Response: " . $result);
        
        $response = json_decode($result, true);

        if (isset($response['data']['attributes']['checkout_url'])) {
            error_log("Debug 7: Got checkout URL, updating payment reference");
            // Update pending_payment with payment reference
            $paymentReference = $response['data']['attributes']['reference_number'];
            file_put_contents('payment_reference.txt', $paymentReference);
            $updateQuery = "UPDATE pending_payments 
                          SET payment_reference = ? 
                          WHERE car_id = ? AND user_id = ? 
                          ORDER BY id DESC LIMIT 1";
            
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([$paymentReference, $carId, 42]);
            
            $db->commit();
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