<?php

require_once '../config/db.php'; // Ensure this returns a PDO connection ($db)

$error = ''; // Initialize an error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Prepare and execute the query using prepared statements
        $stmt = $db->prepare("SELECT * FROM user WHERE user_email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // First check if password matches password_hash
            if (password_verify($password, $user['user_password'])) {
                // Password matches password_hash
                $login_success = true;
            } 
            // If password_hash check fails, try MD5
            else if (md5($password) === $user['user_password']) {
                // Password matches MD5 hash
                $login_success = true;
                
            } else {
                $login_success = false;
            }

            if ($login_success) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['fname'] = $user['user_fname'];
                $_SESSION['mname'] = $user['user_mname'];
                $_SESSION['lname'] = $user['user_lname'];
                $_SESSION['phone'] = $user['user_phone'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['profile_image'] = $user['profile_image'] ?: 'images/profile/user.png';
                $_SESSION['username'] = $user['user_fname'] . ' ' . $user['user_lname'];
                $_SESSION['success_message'] = "Welcome back, " . $user['user_fname'] . "!";

                // Update last login timestamp
                $updateStmt = $db->prepare("UPDATE user SET user_last_login_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
                $updateStmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                $updateStmt->execute();

                // Check if there's a redirect URL
                if (isset($_POST['redirect'])) {
                    header('Location: ' . $_POST['redirect']);
                } else {
                    header('Location: ../dashboard.php');
                }
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
