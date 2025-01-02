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
    <title>Login Account</title>
</head>
<body>
    <?php include 'includes/nav.php' ?>

    <div class="container">
        <div class="row py-5 d-flex align-items-center justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg login-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome back!</h2>
                            <p class="text-muted">Please enter your details</p>
                        </div>
                        
                        <form action="./backend/login_handler.php" method="POST">
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger text-center" role="alert">
                                    <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php elseif (isset($_GET['success'])): ?>
                                <div class="alert alert-success text-center" role="alert">
                                    <?php echo htmlspecialchars($_GET['success']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Email"/>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password" />
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember for 30 days</label>
                                </div>
                                <a href="#" class="text-decoration-none">I forgot my password</a>
                            </div>
                            <button type="submit" class="btn w-100 mb-3 btn-primary">Sign In</button>
                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1 m-0">
                                <span class="mx-3 text-muted">OR</span>
                                <hr class="flex-grow-1 m-0">
                            </div>
                            <button type="button" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2">
                                <img src="images/icons/google.svg" alt="Google" width="20">
                                Sign in with Google
                            </button>
                        </form>
                        <p class="text-center mt-4 mb-0">
                            Don't have an account? <a href="signup.php" class="text-decoration-none">Sign Up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before closing body tag -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
    <script>
    $(document).ready(function() {
        // Check if there's a redirect URL stored
        const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
        
        if (redirectUrl) {
            // Add the redirect URL as a hidden input to the form
            $('form').append(`<input type="hidden" name="redirect" value="${redirectUrl}">`);
        }
    });
    </script>
</body>
<?php include 'includes/scripts.php' ?>
</html>
