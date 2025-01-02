<?php
// Include necessary files
include 'db_conn.php';

// Get the POST data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = $data['rental_id'];

// Check if the rental ID is valid
if (!empty($rental_id)) {
    // Update the rental status to REJECTED
    $sql = "UPDATE rental SET rental_status = 'REJECTED' WHERE rental_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rental_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Rental has been rejected successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to reject the rental.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid rental ID.']);
}
?>
