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

            // Verify the password
            if (password_verify($password, $user['user_password'])) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['fname'] = $user['user_fname'];
                $_SESSION['lname'] = $user['user_lname'];
                $_SESSION['success_message'] = "Welcome back, " . $user['user_fname'] . "!";
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
