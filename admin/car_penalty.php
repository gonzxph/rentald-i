<?php
// Start session to manage success/error messages
session_start();

// Include the database connection
include 'db_conn.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $charge_type = $_POST['charge_type'];
    $charge_amount = $_POST['charge'];
    $description = $_POST['description'];
    $rental_id = $_POST['rental_id']; // Ensure this is passed dynamically
    $date = date('Y-m-d H:i:s'); // Current timestamp

    // Validate required fields
    if (empty($charge_type) || empty($charge_amount) || empty($description) || empty($rental_id)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: car_penalty.php');
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO rent_penalty (rental_id, rent_penalty_type, rent_penalty_amount, rent_penalty_description, rent_penalty_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isdss", $rental_id, $charge_type, $charge_amount, $description, $date);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Penalty or additional charge has been successfully added.';
        } else {
            $_SESSION['error'] = 'Failed to add penalty or additional charge.';
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: Unable to prepare statement.';
    }

    $conn->close();
    header('Location: car_penalty.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
    <title>D&I CEBU CAR RENTAL - Add Penalty or Additional Charge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            margin-bottom: 20px;
            text-align: center;
            color: rgb(0, 0, 0);
        }
        .form-label {
            font-weight: bold;
        }
        .radio-group {
            margin-bottom: 20px;
        }
        .back-btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="outer-box" style="margin-top: 15px;">
            <!-- Success and Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Form Section -->
            <h1 class="form-title">Add Penalty or Additional Charge</h1>
            <div class="form-container">
                <form action="car_penalty.php" method="POST">
                    <!-- Hidden field for rental_id -->
                    <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($_GET['rental_id'] ?? ''); ?>">

                    <!-- Penalty or Additional Charge -->
                    <div class="radio-group">
                        <label class="form-label">Select Type:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="charge_type" id="penalty" value="Penalty" required>
                            <label class="form-check-label" for="penalty">Penalty</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="charge_type" id="additional_charge" value="Additional Charge">
                            <label class="form-check-label" for="additional_charge">Additional Charge</label>
                        </div>
                    </div>

                    <!-- Charge -->
                    <div class="mb-3">
                        <label for="charge" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="charge" name="charge" placeholder="Enter total fee (e.g., 500)" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Provide a detailed explanation for the selected option." required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem;">Submit</button>
                    </div>
                </form>
            </div>

            <!-- Back Button -->
            <div class="back-btn-container">
                <button type="button" class="btn btn-secondary" onclick="goToApproved()">Back</button>
            </div>
        </div>
    </div>

    <script>
        function goToApproved() {
            window.location.href = 'index.php?content=approved_content.php';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
