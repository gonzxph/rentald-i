<?php
session_start();
include 'db_conn.php'; // Include the database connection

$user_id = $_SESSION['user_id'];

// Initialize error array
$errors = [];

// Retrieve admin information from database
try {
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ? AND user_role = 'ADMIN'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    
    if (!$admin) {
        $_SESSION['error_message'] = "Admin not found.";
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error retrieving admin information.";
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $user_fname = trim($_POST['user_fname']);
    $user_mname = trim($_POST['user_mname']);
    $user_lname = trim($_POST['user_lname']);
    $user_phone = trim($_POST['user_phone']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $profile_picture = $_FILES['profile_picture'];

    // Validate inputs
    if (empty($user_fname) || empty($user_lname) || empty($user_phone)) {
        $errors[] = "First Name, Last Name, and Phone Number are required.";
    }

    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Handle profile picture upload
    if (!empty($profile_picture['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($profile_picture['type'], $allowed_types)) {
            $errors[] = "Only JPG and PNG files are allowed for profile pictures.";
        } else {
            $target_dir = "uploads/profile_pictures/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . uniqid('user_', true) . "." . pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
                $errors[] = "Failed to upload profile picture.";
            } else {
                $profile_picture_path = $target_file;
            }
        }
    }

    // If no errors, update the database
    if (empty($errors)) {
        try {
            $query = "UPDATE user SET user_fname = ?, user_mname = ?, user_lname = ?, user_phone = ?";
            $types = "ssss"; // string types for the parameters
            $params = [$user_fname, $user_mname, $user_lname, $user_phone];

            if (!empty($profile_picture_path)) {
                $query .= ", profile_image = ?";
                $types .= "s";
                $params[] = $profile_picture_path;
                $_SESSION['profile_image'] = $profile_picture_path;
            }

            if (!empty($new_password)) {
                $query .= ", user_password = ?";
                $types .= "s";
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $params[] = $hashed_password;
            }

            $query .= " WHERE user_email = ? AND user_role = 'ADMIN'";
            $types .= "s";
            $params[] = $_SESSION['user_email'];

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            // Update session variables
            $_SESSION['fname'] = $user_fname;
            $_SESSION['mname'] = $user_mname;
            $_SESSION['lname'] = $user_lname;
            $_SESSION['phone'] = $user_phone;

            $_SESSION['success_message'] = "Profile updated successfully!";
            header("Location: admin_settings.php");
            exit();
        } catch (Exception $e) {
            $errors[] = "An error occurred while updating the profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Settings</title>
</head>
<body>
<?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav_header.php'; ?>

<!-- Alerts Section -->
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

    <!-- Admin Settings Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-0">Admin Settings</h4>
                    </div>
                    <div class="card-body">
                        <form action="admin_settings.php" method="POST" enctype="multipart/form-data">
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
                                        <input type="text" class="form-control" name="user_fname" value="<?php echo htmlspecialchars($_SESSION['fname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="user_mname" value="<?php echo htmlspecialchars($_SESSION['mname'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="user_lname" value="<?php echo htmlspecialchars($_SESSION['lname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="user_phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" 
                                            value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" 
                                            readonly>
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

                           
                                <div class="mt-4 d-flex justify-content-between">
                                    <button type="button" class="btn btn-primary" onclick="goToDashboard()">Back</button>
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

function goToDashboard() {
            window.location.href = 'index.php';
        }
    // Preview uploaded profile picture
    document.getElementById('profilePicture').addEventListener('change', function (event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('profilePreview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
</body>
</html>



