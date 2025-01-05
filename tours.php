<?php
require_once 'includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Coming Soon!</title>
    <?php include 'includes/head.php'; ?>
    
    <style>
        .bg-gradient {
            background: linear-gradient(180deg, #e3f2fd 0%, #bbdefb 100%);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-gradient">
        <main class="text-center container">
            <h1 class="display-3 fw-bold text-primary mb-4">
                Coming Soon
            </h1>
            <p class="fs-4 text-primary-emphasis mb-5">
                Our tours section is under construction. We'll be here soon with exciting new adventures!
            </p>
            
            <!-- Loading Animation -->
            <div class="spinner-border text-primary mb-5" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>

            <!-- Email Notification Form -->
            <div class="mx-auto" style="max-width: 500px;">
                <h2 class="h4 fw-semibold text-primary mb-4">
                    Get notified when we launch:
                </h2>
                <form class="d-flex justify-content-center" id="notifyForm">
                    <div class="input-group">
                        <input 
                            type="email" 
                            class="form-control"
                            placeholder="Enter your email" 
                            required
                        >
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="fas fa-envelope"></i>
                            Notify Me
                        </button>
                    </div>
                </form>
            </div>
        </main>
        </div>
        <!-- Replace the simple footer with the included footer -->
        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- Replace the direct script tags with the included scripts -->
    <?php include 'includes/scripts.php'; ?>
</body>
</html> 