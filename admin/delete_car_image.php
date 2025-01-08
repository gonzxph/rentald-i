<?php
session_start();
include "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id']) && isset($_POST['image_name'])) {
    $car_id = $_POST['car_id'];
    $image_name = $_POST['image_name'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete from database
        $query = "DELETE FROM car_image WHERE car_id = ? AND img_url = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'is', $car_id, $image_name);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error deleting image from database");
        }
        
        // Delete file from server
        $file_path = "../upload/car/" . $image_name;
        if (file_exists($file_path) && !unlink($file_path)) {
            throw new Exception("Error deleting image file");
        }
        
        mysqli_commit($conn);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
} 