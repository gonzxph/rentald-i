<?php
// Include database connection
include 'db_conn.php';

// Get the penalty ID from the URL
$penalty_id = isset($_GET['id']) ? $_GET['id'] : '';

// Check if the ID is valid
if ($penalty_id) {
    // SQL query to soft delete the penalty
    $sql = "UPDATE rent_penalty SET is_deleted = 1 WHERE rent_penalty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $penalty_id);
    
    if ($stmt->execute()) {
        // Redirect back to the previous page with a success message
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
