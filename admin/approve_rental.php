<?php
// Include database connection
include 'db_conn.php';

// Get the rental ID from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = $data['rental_id'];

// Update the rental status to "APPROVED"
$sql = "UPDATE rental SET rental_status = 'APPROVED' WHERE rental_id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);

// Execute the query and check if the update was successful
if ($stmt->execute()) {
    // Respond with success
    echo json_encode(['success' => true, 'message' => 'Rental has been approved and updated.']);
} else {
    // Respond with error
    echo json_encode(['success' => false, 'error' => 'Failed to approve rental.']);
}
?>
