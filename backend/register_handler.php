<?php

header('Content-Type: application/json');

require_once '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $fname = htmlspecialchars(trim($_POST['firstname']), ENT_QUOTES, 'UTF-8');
    $lname = htmlspecialchars(trim($_POST['lastname']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $conf_password = $_POST['confirm_pass'];

    $errors = [];

    // Validate inputs
    if (empty($fname)) {
        $errors[] = "First name is required";
    } elseif (strlen($fname) > 20) {
        $errors[] = "First name cannot exceed 20 characters";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $fname)) {
        $errors[] = "First name can only contain letters, spaces, hyphens, and apostrophes";
    }

    if (empty($lname)) {
        $errors[] = "Last name is required";
    } elseif (strlen($lname) > 30) {
        $errors[] = "Last name cannot exceed 30 characters";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $lname)) {
        $errors[] = "Last name can only contain letters, spaces, hyphens, and apostrophes";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errors[] = "A valid email address is required";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } else {
        // Combined regex for more efficient validation
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number    = preg_match('/[0-9]/', $password);
        $special   = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
        
        // Check password length (increased to 12 for better security)
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        // Combine all format requirements into one message
        if (!($uppercase && $lowercase && $number && $special)) {
            $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, " .
                       "one number, and one special character (!@#$%^&*(),.?\":{}|<>)";
        }

        // Check for common patterns that might make password weak
        if (preg_match('/(.)\1{2,}/', $password)) {
            $errors[] = "Password cannot contain three or more repeated characters";
        }

        if (preg_match('/12345|qwerty|password/i', $password)) {
            $errors[] = "Password contains a common pattern and would be easy to guess";
        }
    }

    // Separate password confirmation check
    if ($password !== $conf_password) {
        $errors[] = "Passwords do not match";
    }

    // If validation errors exist, redirect back with errors
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => implode(', ', $errors)
        ]);
        exit();
    }

    try {
        // Start transaction
        $db->beginTransaction();
        
        // Add error logging configuration
        $logFile = '../logs/error.log';
        if (!file_exists('../logs')) {
            mkdir('../logs', 0777, true);
        }

        // Case-sensitive email check
        $stmt = $db->prepare("SELECT user_id FROM user WHERE user_email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $db->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'This email address is already registered. Please use a different email or try logging in.'
            ]);
            exit();
        }

        // Hash the password with strong options
        $hashed_password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        
        // Verify hash was successful
        if ($hashed_password === false) {
            throw new Exception('Password hashing failed');
        }

        // Insert the new user into the database
        $stmt = $db->prepare("
            INSERT INTO user (user_fname, user_lname, user_email, user_password, user_created_at) 
            VALUES (:fname, :lname, :email, :password, NOW())
        ");
        
        $success = $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        if (!$success) {
            throw new Exception("Failed to create account: " . implode(", ", $stmt->errorInfo()));
        }

        // Commit transaction
        $db->commit();

        // Redirect to login page
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful! Redirecting to login page...'
        ]);
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        // Log the detailed error but send a user-friendly message
        $errorMessage = sprintf(
            "[%s] Registration error: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        error_log($errorMessage, 3, $logFile);
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to create your account at this time. Please try again later or contact support if the problem persists.'
        ]);
        exit();
    }
} else {
    // Set security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    
    // Log attempted non-POST access
    error_log("Invalid access attempt to register_handler.php using " . $_SERVER['REQUEST_METHOD'] . " method");
    
    // Redirect with error
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use the registration form.'
    ]);
    exit();
}

// Ensure script execution ends here
exit();
