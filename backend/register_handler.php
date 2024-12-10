<?php

require_once '../config/db.php'; // Ensure this returns a PDO connection ($db)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $conf_password = $_POST['confirm_pass'];

    $errors = [];

    // Validate inputs
    if (empty($fname)) {
        $errors[] = "First name is required.";
    }

    if (empty($lname)) {
        $errors[] = "Last name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $conf_password) {
        $errors[] = "Passwords do not match.";
    }

    // If validation errors exist, redirect back with errors
    if (!empty($errors)) {
        header("Location: ../signup.php?error=" . urlencode(implode(', ', $errors)));
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $db->prepare("SELECT user_id FROM user WHERE user_email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            header("Location: ../signup.php?error=" . urlencode("Email is already registered."));
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $db->prepare(
            "INSERT INTO user (user_fname, user_lname, user_email, user_password) VALUES (:fname, :lname, :email, :password)"
        );
        $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
        $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect to a success page or login page
        header("Location: ../login.php?success=Registration successful! Please login.");
        exit();

    } catch (PDOException $e) {
        header("Location: ../signup.php?error=" . urlencode("An error occurred: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: ../signup.php?error=" . urlencode("Invalid request method."));
    exit();
}
