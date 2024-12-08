<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>Create account</title>
</head>
<body>

    <?php include 'includes/nav.php' ?>
    
    <div class="container">
        <div class="row min-vh-100 d-flex align-items-center justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg signup-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create an account</h2>
                            <p class="text-muted">Please enter your details</p>
                        </div>
                        
                        <form action="./backend/register_handler.php" method="POST">
                            <!-- Error/Success messages -->
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php elseif (isset($_GET['success'])): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo htmlspecialchars($_GET['success']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="firstname">First name</label>
                                    <input type="text" id="firstname" name="firstname" class="form-control form-control-lg" placeholder="John">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="lastname">Last name</label>
                                    <input type="text" id="lastname" name="lastname" class="form-control form-control-lg" placeholder="Doe">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="johndoe@gmail.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="confirm_pass">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_pass" name="confirm_pass" class="form-control form-control-lg" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-decoration-none">Terms & Conditions</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 mb-3">Create account</button>
                            <button type="button" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2">
                                <img src="images/icons/google.svg" alt="Google" width="20">
                                Sign up with Google
                            </button>
                        </form>
                        <p class="text-center mt-4 mb-0">
                            Already have an account? <a href="login.php" class="text-decoration-none">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

                    <h6>2. User Registration</h6>
                    <p>Users must provide accurate and complete information during registration. Users are responsible for maintaining the confidentiality of their account information.</p>

                    <h6>3. Privacy Policy</h6>
                    <p>Your use of our service is also governed by our Privacy Policy. Please review our Privacy Policy to understand our practices.</p>

                    <!-- Add more terms as needed -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            togglePasswordVisibility(password, icon);
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const password = document.getElementById('confirm_pass');
            const icon = this.querySelector('i');
            togglePasswordVisibility(password, icon);
        });

        function togglePasswordVisibility(input, icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
<?php include 'includes/scripts.php' ?>
</html>