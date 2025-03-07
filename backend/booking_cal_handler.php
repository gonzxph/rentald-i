<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        
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
        $userId = $_SESSION['user_id'];
        // Recalculate total rental fee
        $totalRentalFee = $vehicleRate + $excessPay + $deliveryFee + $returnFee;
        
        // Calculate payment amount based on option
        $paymentAmount = ($paymentOption === 'reservation') ? 500 : $totalRentalFee;
        
        // Before DB transaction
        $db->beginTransaction();

        // Get driver information
        $isCustomDriver = $_POST['isCustomDriver'] === '1';
        $driverName = $isCustomDriver ? $_POST['driverName'] : null;
        $driverPhone = $isCustomDriver ? $_POST['driverPhone'] : null;
        $driverLicense = $isCustomDriver ? $_POST['driverLicense'] : null;

        // Handle file uploads if custom driver
        $driverIdImages = [];
        if ($isCustomDriver && isset($_FILES['file-input'])) {
            $tempUploadDir = '../upload/temp/';
            
            if (!file_exists($tempUploadDir)) {
                mkdir($tempUploadDir, 0777, true);
            }
            
            $fileCount = count($_FILES['file-input']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = uniqid() . '_' . $_FILES['file-input']['name'][$i];
                $tmpName = $_FILES['file-input']['tmp_name'][$i];
                $targetPath = $tempUploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $driverIdImages[] = $fileName;
                }
            }
            
            // Store filenames in pending_payments instead of session
            $imageJson = json_encode($driverIdImages);
        }

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
            is_custom_driver,
            custom_driver_name,
            custom_driver_phone,
            custom_driver_license_number,
            temp_driver_images,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $db->prepare($pendingPaymentQuery);
        $stmt->execute([
            $carId,
            $userId,
            $paymentOption,
            $pickupAddress,
            $pickupDate,
            $returnAddress,
            $returnDate,
            $vehicleRate,
            $deliveryFee,
            $returnFee,
            $totalRentalFee,
            $paymentAmount,
            $isCustomDriver,
            $driverName,
            $driverPhone,
            $driverLicense,
            $imageJson ?? null,
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
            $stmt->execute([$paymentReference, $carId, $userId]);
            
            $db->commit();

            // Store payment information in session
            $_SESSION['payment_checkout_url'] = $response['data']['attributes']['checkout_url'];
            $_SESSION['payment_reference'] = $paymentReference;
            
            // Redirect to an intermediate payment page
            header("Location: ../process_payment.php");
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