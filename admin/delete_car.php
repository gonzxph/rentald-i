<?php
session_start();
include "db_conn.php";

// Check if car_id is provided
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    // Delete car record from the car_image table
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

    // Redirect directly to car_list.php after deletion
    header("Location: car_list.php");
    exit();
} else {
    $_SESSION['error'] = "No car ID provided.";
    header("Location: car_list.php");
    exit();
}
