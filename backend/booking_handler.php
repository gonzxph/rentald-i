<?php

require_once __DIR__ . '/../config/db.php';

try{
    if(!isset($_GET['carid'])){
        header('Location: index.php');
    }


    $pickup_datetime = isset($_GET['pickup']) ? htmlspecialchars($_GET['pickup']) : null;
    $dropoff_datetime = isset($_GET['dropoff']) ? htmlspecialchars($_GET['dropoff']) : null;

    // Convert the raw values to a readable format
    $pickup_datetime_formatted = $pickup_datetime 
    ? (new DateTime($pickup_datetime))->format('F d, Y \a\t h:i A') 
    : null;

    $dropoff_datetime_formatted = $pickup_datetime 
    ? (new DateTime($pickup_datetime))->format('F d, Y \a\t h:i A') 
    : null;

    $carid = (int) $_GET['carid'];

    $sql = "SELECT * FROM car WHERE car_id = :carid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':carid', $carid, PDO::PARAM_INT);
    $stmt->execute();

    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    
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


/* require_once 'views/booking_view.php'; */

?>