<?php
// Include necessary files
include 'db_conn.php';

// Get the POST data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = isset($data['rental_id']) ? $data['rental_id'] : null;

// Check if the rental ID is valid
if ($rental_id === null) {
    echo json_encode(['success' => false, 'error' => 'Rental ID is required.']);
    exit;
}

try {
    // Start a transaction
    $conn->begin_transaction();

    // Update the rental status to "REJECTED" and set the rejection datetime
    $sql = "UPDATE rental SET rent_status = 'REJECTED', rent_rejected_datetime = NOW() WHERE rental_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing rental rejection query: " . $conn->error);
    }
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Respond with success
    echo json_encode(['success' => true, 'message' => 'Rental has been rejected successfully.']);
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    // Close the statement
    if (isset($stmt)) $stmt->close();

    // Close the database connection
    $conn->close();
}
?>
