<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: signin.php');
    exit;
}

// Fetch current user data from database
try {
    $stmt = $db->prepare("SELECT * FROM user WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Update all relevant session variables
        $_SESSION['fname'] = $user['user_fname'];
        $_SESSION['mname'] = $user['user_mname'];
        $_SESSION['lname'] = $user['user_lname'];
        $_SESSION['phone'] = $user['user_phone'];
        $_SESSION['user_email'] = $user['user_email'];
        $_SESSION['profile_image'] = $user['profile_image'] ?: 'images/profile/user.png';
        $_SESSION['username'] = $user['user_fname'] . ' ' . $user['user_lname'];
    }
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate required fields
    if (empty($_POST['fname'])) $errors[] = "First name is required";
    if (empty($_POST['lname'])) $errors[] = "Last name is required";
    if (empty($_POST['phone'])) $errors[] = "Phone number is required";
    
    // Validate password if being changed
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $errors[] = "Passwords do not match";
        }
    }
    
    // Handle profile picture upload
    $profile_image = $_SESSION['profile_image'] ?? 'images/profile/user.png';
    if (!empty($_FILES['profile_picture']['name'])) {
        $allowed = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['profile_picture']['type'], $allowed)) {
            $errors[] = "Only JPG and PNG files are allowed";
        } else {
            $upload_dir = 'images/profile/';
            $filename = uniqid() . '_' . $_FILES['profile_picture']['name'];
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename)) {
                $profile_image = $upload_dir . $filename;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // If no errors, update the database
    if (empty($errors)) {
        try {
            $sql = "UPDATE user SET 
                    user_fname = :fname,
                    user_mname = :mname,
                    user_lname = :lname,
                    user_phone = :phone,
                    profile_image = :profile_image";
            
            $params = [
                ':fname' => $_POST['fname'],
                ':mname' => $_POST['mname'],
                ':lname' => $_POST['lname'],
                ':phone' => $_POST['phone'],
                ':profile_image' => $profile_image
            ];
            
            // Add password update if provided
            if (!empty($_POST['new_password'])) {
                $sql .= ", user_password = :password";
                $params[':password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE user_id = :user_id";
            $params[':user_id'] = $_SESSION['user_id'];
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            // Update session variables
            $_SESSION['fname'] = $_POST['fname'];
            $_SESSION['mname'] = $_POST['mname'];
            $_SESSION['lname'] = $_POST['lname'];
            $_SESSION['phone'] = $_POST['phone'];
            $_SESSION['profile_image'] = $profile_image;
            $_SESSION['username'] = $_POST['fname'] . ' ' . $_POST['lname'];
            
            $_SESSION['success_message'] = "Profile updated successfully!";
            header('Location: settings.php');
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Account Settings - D&I Car Rental & Tours</title>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Alerts section -->
    <div class="min-vh-100">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success fade show d-flex justify-content-center" role="alert" id="successAlert">
                <?php 
                    echo htmlspecialchars($_SESSION['success_message']); 
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger fade show d-flex justify-content-center" role="alert" id="errorAlert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-0">Account Settings</h4>
                    </div>
                    <div class="card-body">
                        <form action="settings.php" method="POST" enctype="multipart/form-data">
                            <!-- Profile Picture -->
                            <div class="mb-4 text-center">
                                <img src="<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'images/profile/user.png'; ?>" 
                                     alt="Profile Picture" 
                                     class="rounded-circle mb-3" 
                                     width="150" 
                                     height="150"
                                     id="profilePreview">
                                <div>
                                    <input type="file" class="form-control d-none" id="profilePicture" name="profile_picture" accept="image/jpeg,image/png">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('profilePicture').click()">
                                        <i class="fas fa-camera me-2"></i>Upload new picture
                                    </button>
                                    <div class="small text-muted mt-1">Only JPG and PNG files are allowed</div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($_SESSION['fname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="mname" value="<?php echo htmlspecialchars($_SESSION['mname'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($_SESSION['lname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Change Section -->
                            <div class="mt-4">
                                <h5>Change Password</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="new_password">
                                            <div class="small text-muted">Leave blank to keep current password</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
    <script>
        // Add this new script block
        $(document).ready(function() {
            // Handle success alert
            const $successAlert = $("#successAlert");
            if ($successAlert.length) {
                setTimeout(function() {
                    $successAlert.fadeOut("slow", function() {
                        $(this).remove();
                    });
                }, 2000);
            }

            // Handle error alert
            const $errorAlert = $("#errorAlert");
            if ($errorAlert.length) {
                setTimeout(function() {
                    $errorAlert.fadeOut("slow", function() {
                        $(this).remove();
                    });
                }, 2000);
            }
        });

        // Preview uploaded image
        document.getElementById('profilePicture').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html> 