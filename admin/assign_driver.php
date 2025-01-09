<?php
include 'db_conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_POST['driver_id'] ?? null;
    $rental_id = $_POST['rental_id'] ?? null;

    if (!empty($driver_id) && !empty($rental_id)) {
        $sql = "UPDATE rental SET assigned_driver_id = ? WHERE rental_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $driver_id, $rental_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Driver successfully assigned.';
            } else {
                $_SESSION['error'] = 'Failed to assign driver.';
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = 'Database error: Unable to prepare statement.';
        }
    } else {
        $_SESSION['error'] = 'Invalid input.';
    }

    header("Location: approved_rent_details.php?rental_id=$rental_id");
    exit();
}
?>
