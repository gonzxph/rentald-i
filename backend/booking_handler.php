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

    // Check if car is already booked for these dates
    $check_sql = "SELECT COUNT(*) FROM rental 
                  WHERE car_id = :carid 
                  AND (
                      (RENT_PICKUP_DATETIME <= :end_datetime AND RENT_DROPOFF_DATETIME >= :start_datetime)
                      OR (RENT_PICKUP_DATETIME <= :start_datetime AND RENT_DROPOFF_DATETIME >= :start_datetime)
                      OR (RENT_PICKUP_DATETIME <= :end_datetime AND RENT_DROPOFF_DATETIME >= :end_datetime)
                  )";
    
    // Prepare and execute the check query
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':carid', $carid, PDO::PARAM_INT);
    $check_stmt->bindParam(':start_datetime', $pickup_datetime);
    $check_stmt->bindParam(':end_datetime', $dropoff_datetime);
    $check_stmt->execute();
    
    if ($check_stmt->fetchColumn() > 0) {
        // Car is already booked for these dates
        header('Location: search.php?error=already_booked');
        exit;
    }

    $sql = "SELECT * FROM car WHERE car_id = :carid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':carid', $carid, PDO::PARAM_INT);
    $stmt->execute();

    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    //Car rate calculation
    $carRentalRate = isset($car['car_rental_rate']) ? floatval($car['car_rental_rate']) : 0;
    $carExcessPerHour = isset($car['car_excess_per_hour']) ? floatval($car['car_excess_per_hour']) : 0;
    $bookingDurationDay1 = isset($bookingDurationDay) ? intval($bookingDurationDay) : 0;
    $bookingDurationHour1 = isset($bookingDurationHour) ? intval($bookingDurationHour) : 0;

    if ($bookingDurationHour > 6) {
        // Increment the booking days by 1 for hours above 6
        $bookingDurationDay1 += 1;
        $bookingDurationHour1 = 0; // Reset excess hours
    }

    $carRentalRate = $carRentalRate * $bookingDurationDay1;
    $carExcessPay = $carExcessPerHour * $bookingDurationHour1;
    $totalRate = $carRentalRate;

    if(!$car){
        throw new Exception('Car not found');
    }

    // Fetch car images
    $sqlImages = "SELECT img_url FROM car_image WHERE car_id = :carid";
    $stmtImages = $db->prepare($sqlImages);
    $stmtImages->bindParam(':carid', $carid, PDO::PARAM_INT);
    $stmtImages->execute();
    $carImages = $stmtImages->fetchAll(PDO::FETCH_COLUMN); // Fetch image paths as array

}catch (PDOException $e) {
    // Handle database errors
    error_log("Database error: " . $e->getMessage());
    $error_message = "A database error occurred. Please try again later.";
} catch (Exception $e) {
    // Handle other errors
    $error_message = $e->getMessage();
}

?>