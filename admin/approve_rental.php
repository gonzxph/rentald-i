<?php
// Include database connection
include 'db_conn.php';

// Get the rental ID from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = isset($data['rental_id']) ? $data['rental_id'] : null;

if ($rental_id === null) {
    echo json_encode(['success' => false, 'error' => 'Rental ID is required.']);
    exit;
}

try {
    // Start a transaction
    $conn->begin_transaction();

    // Update the rental status to "APPROVED" and set the approval datetime
    $sql1 = "UPDATE rental SET rent_status = 'APPROVED', rent_approved_datetime = NOW() WHERE rental_id = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        throw new Exception("Error preparing rental update query: " . $conn->error);
    }
    $stmt1->bind_param("i", $rental_id);
    $stmt1->execute();

    // Update the pay_type to "Full Payment"
    $sql2 = "UPDATE payment SET pay_type = 'fullPayment' WHERE rental_id = ?";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        throw new Exception("Error preparing payment update query: " . $conn->error);
    }
    $stmt2->bind_param("i", $rental_id);
    $stmt2->execute();

    // Commit the transaction
    $conn->commit();

    // Respond with success
    echo json_encode(['success' => true, 'message' => 'Rental has been approved and updated.']);
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    // Close the statements
    if (isset($stmt1)) $stmt1->close();
    if (isset($stmt2)) $stmt2->close();

    // Close the database connection
    $conn->close();
}
?>
