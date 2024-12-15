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

        // Recalculate total rental fee
        $totalRentalFee = $vehicleRate + $excessPay + $deliveryFee + $returnFee;
        
        // Calculate payment amount based on option
        $paymentAmount = ($paymentOption === 'reservation') ? 500 : $totalRentalFee;

        // Store booking details in session
        $_SESSION['booking_details'] = [
            'car_id' => $carId,
            'payment_option' => $paymentOption,
            'pickup_address' => $pickupAddress,
            'pickup_date' => $pickupDate,
            'return_address' => $returnAddress,
            'return_date' => $returnDate,
            'vehicle_rate' => $vehicleRate,
            'excess_pay' => $excessPay,
            'delivery_fee' => $deliveryFee,
            'return_fee' => $returnFee,
            'total_rental_fee' => $totalRentalFee,
            'payment_amount' => $paymentAmount
        ];

        // Create PayMongo payment link
        $secretKey = "sk_test_6XrwLnwC4nHDCf4CuTLxDcKq";
        
        // Convert amount to centavo
        $amount = $paymentAmount * 100;
        
        $description = $paymentOption === 'reservation' 
            ? "Reservation Fee for Car Rental" 
            : "Full Payment for Car Rental";

        $data = [
            "data" => [
                "attributes" => [
                    "amount" => $amount,
                    "currency" => "PHP",
                    "description" => $description,
                    "remarks" => "Car Rental Payment",
                    "success_url" => "https://yourwebsite.com/backend/payment_success.php",
                    "failure_url" => "https://yourwebsite.com/backend/payment_failure.php"
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($secretKey . ":")
        ]);

        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response['data']['attributes']['checkout_url'])) {
            header('Location: ' . $response['data']['attributes']['checkout_url']);
            exit();
        } else {
            throw new Exception("Error creating payment link");
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../booking.php?carid=' . $carId);
        exit();
    }
}
?>