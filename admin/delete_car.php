<?php
session_start();
include "db_conn.php";

// Check if car_id is provided
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    // First check if the car has any existing rentals
    $check_rental_sql = "SELECT COUNT(*) as rental_count FROM rental WHERE car_id = '$car_id'";
    $rental_result = mysqli_query($conn, $check_rental_sql);
    $rental_data = mysqli_fetch_assoc($rental_result);

    if ($rental_data['rental_count'] > 0) {
        // Car has existing rentals
        $_SESSION['error'] = "Cannot delete this vehicle because it has existing rental records.";
        header("Location: car_list.php");
        exit();
    }

    // If no rentals exist, proceed with deletion
    // Delete car record from the car_image table first
    $delete_image_sql = "DELETE FROM car_image WHERE car_id = '$car_id'";
    if (mysqli_query($conn, $delete_image_sql)) {
        // Delete car record from the car table
        $delete_car_sql = "DELETE FROM car WHERE car_id = '$car_id'";
        if (mysqli_query($conn, $delete_car_sql)) {
            $_SESSION['success'] = "Car deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting car: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Error deleting car images: " . mysqli_error($conn);
    }

    header("Location: car_list.php");
    exit();
} else {
    $_SESSION['error'] = "No car ID provided.";
    header("Location: car_list.php");
    exit();
}
