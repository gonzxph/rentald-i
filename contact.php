<?php
require_once 'includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Contact Us - D&I Car Rental & Tours</title>
    
    <!-- Custom CSS -->
    <style>
        h1 {
            color: #024B88;
        }
      
        .social-icons a {
            color: #024B88;
        }
    </style>
</head>
<body>
    <!-- Include your navigation here -->
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="container py-5">
        <h1 class="text-center text-primary mb-2">Contact us</h1>
        
        <p class="text-center mb-4">
            Do you have any questions or do you need help?<br>
            Feel free to contact us.
        </p>

        <!-- Contact Information -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="d-flex flex-column gap-3">
                    <!-- Phone Numbers -->
                    <a href="tel:+639638051183" class="text-decoration-none text-dark d-block">
                        <i class="fas fa-phone me-2"></i>+63 963 805 1183
                    </a>
                    
                    <a href="tel:+639086046641" class="text-decoration-none text-dark d-block">
                        <i class="fas fa-phone me-2"></i>+63 908 604 6641
                    </a>

                    <!-- WhatsApp -->
                    <a href="#" class="text-decoration-none text-dark">
                        <i class="fab fa-whatsapp me-2"></i>D & I Car Rental & Tours WhatsApp
                    </a>

                    <!-- Email -->
                    <a href="mailto:dicarrentaltours@gmail.com" class="text-decoration-none text-dark">
                        <i class="far fa-envelope me-2"></i>dicarrentaltours@gmail.com
                    </a>

                    <!-- Skype -->
                    <a href="skype:dicarrentaltours?chat" class="text-decoration-none text-dark">
                        <i class="fab fa-skype me-2"></i>dicarrentaltours
                    </a>

                    <!-- Social Media Links -->
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <a href="https://www.facebook.com/profile.php?id=100078937516633" target="_blank" class="text-decoration-none text-primary fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-decoration-none text-primary fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-decoration-none text-primary fs-4"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="h5 mb-4">Use our contact form to send us a message!</h2>
                
                <form action="process_contact.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="email" class="form-control" name="email" placeholder="Your email" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" placeholder="Your name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="8" placeholder="Type your concern here" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Send</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include your footer here -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 