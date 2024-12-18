<?php

require_once '../config/db.php'; // Ensure this returns a PDO connection ($db)

$error = ''; // Initialize an error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Updated query with correct table name case and status check
        $stmt = $db->prepare("SELECT * FROM USER WHERE USER_EMAIL = :email AND USER_STATUS = 'active'");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['USER_PASSWORD'])) { // Note: Updated column name case
                session_start();
                $_SESSION['user_id'] = $user['USER_ID'];
                $_SESSION['logged_in'] = true;
                $_SESSION['fname'] = $user['USER_FNAME'];
                $_SESSION['lname'] = $user['USER_LNAME'];
                
                // Update user online status
                $updateStmt = $db->prepare("UPDATE USER SET USER_IS_ONLINE = 1 WHERE USER_ID = :user_id");
                $updateStmt->bindParam(':user_id', $user['USER_ID'], PDO::PARAM_INT);
                $updateStmt->execute();
                
                $_SESSION['success_message'] = "Welcome back, " . $user['USER_FNAME'] . "!";
                header('Location: ../dashboard.php');
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
} else {
    $error = "Invalid request.";
}

// Redirect back to the login page with the error message
header("Location: ../signin.php?error=" . urlencode($error));
exit();
