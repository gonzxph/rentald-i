<?php

require_once __DIR__ . '/../config/db.php';

try{
    if(!isset($_GET['carid'])){
        header('Location: index.php');
    }

    $pickup_datetime = isset($_GET['pickup']) ? htmlspecialchars($_GET['pickup']) : null;
    $dropoff_datetime = isset($_GET['dropoff']) ? htmlspecialchars($_GET['dropoff']) : null;

    $bookingDurationDay = isset($_GET['day']) ? htmlspecialchars($_GET['day']) : null;
    $bookingDurationHour = isset($_GET['hour']) ? htmlspecialchars($_GET['hour']) : null;

    // Convert the raw values to a readable format
    $pickup_datetime_formatted = $pickup_datetime 
    ? (new DateTime($pickup_datetime))->format('F d, Y \a\t h:i A') 
    : null;

    $pickup_time = $pickup_datetime 
    ? (new DateTime($pickup_datetime))->format('H:i') 
    : null;

    $dropoff_datetime_formatted = $dropoff_datetime 
    ? (new DateTime($dropoff_datetime))->format('F d, Y \a\t h:i A') 
    : null;

    $dropoff_time = $dropoff_datetime 
    ? (new DateTime($dropoff_datetime))->format('H:i') 
    : null;

    $carid = (int) $_GET['carid'];

    $sql = "SELECT 
        car_id,
        car_brand,
        car_model,
        car_type,
        car_transmission_type,
        car_seats,
        car_rental_rate,
        car_excess_per_hour,
        car_availability
    FROM car 
    WHERE car_id = :carid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':carid', $carid, PDO::PARAM_INT);
    $stmt->execute();

    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$car) {
        throw new Exception('Car not found');
    }

    // Add validation to ensure required fields exist
    $required_fields = ['car_brand', 'car_model', 'car_type'];
    foreach ($required_fields as $field) {
        if (!isset($car[$field]) || empty($car[$field])) {
            throw new Exception("Missing required car information: $field");
        }
    }

    //Car rate calculation
    $carRentalRate = isset($car['car_rental_rate']) ? floatval($car['car_rental_rate']) : 0;
    $carExcessPerHour = isset($car['car_excess_per_hour']) ? floatval($car['car_excess_per_hour']) : 0;
    $bookingDurationDay1 = isset($bookingDurationDay) ? intval($bookingDurationDay) : 0;
    $bookingDurationHour1 = isset($bookingDurationHour) ? intval($bookingDurationHour) : 0;

    // If excess hours are more than 6, convert to an additional day
    if ($bookingDurationHour1 > 6) {
        $bookingDurationDay1 += 1;
        $bookingDurationHour1 = 0; // Reset excess hours since we're charging a full day
        $carExcessPay = 0; // No excess hour charges when converting to a full day
    } else {
        // Calculate excess hour charges only if 6 hours or less
        $carExcessPay = $carExcessPerHour * $bookingDurationHour1;
    }

    // Calculate total rental rate for the days
    $carRentalRate = $carRentalRate * $bookingDurationDay1;

    // Total rate includes both the daily rate and any excess hour charges
    $totalRate = $carRentalRate + $carExcessPay;


    


    
    if(!$car){
        throw new Exception('Car not found');
    }

    // Fetch car images
    $sqlImages = "SELECT img_url FROM car_image WHERE car_id = :carid";
    $stmtImages = $db->prepare($sqlImages);
    $stmtImages->bindParam(':carid', $carid, PDO::PARAM_INT);
    $stmtImages->execute();
    $carImages = $stmtImages->fetchAll(PDO::FETCH_COLUMN); // Fetch image paths as array

    error_log("Car ID received: " . $_GET['carid']);
    error_log("Car data fetched: " . print_r($car, true));

}catch (PDOException $e) {
    // Handle database errors
    error_log("Database error: " . $e->getMessage());
    $error_message = "A database error occurred. Please try again later.";
} catch (Exception $e) {
    // Handle other errors
    $error_message = $e->getMessage();
}


/* require_once 'views/booking_view.php'; */

?>