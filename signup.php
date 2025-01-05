<?php
session_start();

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // or wherever you want to redirect logged-in users
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>Create account</title>
</head>
<body>

    <?php include 'includes/nav.php' ?>
    
    <div class="container min-vh-100">
        <div class="row py-5 d-flex align-items-center justify-content-center flex-grow-1">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg signup-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create an account</h2>
                            <p class="text-muted">Please enter your details</p>
                        </div>
                        
                        <form id="signupForm" action="./backend/register_handler.php" method="POST">
                            <div id="alertMessages"></div>
                            
                            <div class="mb-3">
                                <input type="text" id="firstname" name="firstname" class="form-control form-control-lg" placeholder="First name *" autocomplete="given-name">
                            </div>
                            <div class="mb-3">
                                <input type="text" id="lastname" name="lastname" class="form-control form-control-lg" placeholder="Last name *" autocomplete="family-name">
                            </div>
                            <div class="mb-3">
                                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Email *" autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password *" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                    <div class="input-group">
                                    <input type="password" id="confirm_pass" name="confirm_pass" class="form-control form-control-lg" placeholder="Confirm Password *" autocomplete="new-password">
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
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>
                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1 m-0">
                                <span class="mx-3 text-muted">OR</span>
                                <hr class="flex-grow-1 m-0">
                            </div>
                            <button type="button" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2">
                                <img src="images/icons/google.svg" alt="Google" width="20">
                                Sign up with Google
                            </button>
                        </form>
                        <p class="text-center mt-4 mb-0">
                            Already have an account? <a href="signin.php" class="text-decoration-none">Log in</a>
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
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php' ?>
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

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('./backend/register_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertDiv = document.getElementById('alertMessages');
                
                if (data.status === 'error') {
                    alertDiv.innerHTML = `
                        <div class="alert alert-danger text-center" role="alert">
                            ${data.message}
                        </div>`;
                } else if (data.status === 'success') {
                    alertDiv.innerHTML = `
                        <div class="alert alert-success text-center" role="alert">
                            ${data.message}
                        </div>`;
                    // Redirect to login page after successful registration
                    setTimeout(() => {
                        window.location.href = 'signin.php';
                    }, 2000);
                }
            })
            .catch(error => {
                document.getElementById('alertMessages').innerHTML = `
                    <div class="alert alert-danger text-center" role="alert">
                        An error occurred. Please try again later.
                    </div>`;
            });
        });
    </script>
</body>
</html>