<?php
session_start();
include 'db_conn.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (empty($_POST['firstName']) || empty($_POST['lastName']) || 
        empty($_POST['email']) || empty($_POST['password']) || 
        empty($_POST['role'])) {
        throw new Exception('All fields are required');
    }

    // Sanitize input
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if email already exists
    $check_query = "SELECT user_id FROM user WHERE user_email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        throw new Exception('Email already exists');
    }

    // Insert new user
    $query = "INSERT INTO user (user_fname, user_lname, user_email, user_password, user_role) 
              VALUES ('$firstName', '$lastName', '$email', '$password', '$role')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'User added successfully'
        ]);
    } else {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}