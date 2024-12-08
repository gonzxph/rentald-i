<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="images/logo/logo1.png" alt="Stratlab Logo" height="40">
        </a>

        <!-- Hamburger menu for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Center menu items -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item mx-2">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active-nav' : ''; ?>" href="index.php">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="vehicles.php">
                        <i class="fas fa-car me-1"></i>Vehicles
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="tours.php">
                        <i class="fas fa-map-marked-alt me-1"></i>Tours
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="contact.php">
                        <i class="fas fa-envelope me-1"></i>Contact Us
                    </a>
                </li>
            </ul>

            <!-- Right-aligned buttons -->
            <div class="navbar-nav">
                <a class="btn btn-outline-primary rounded-pill px-4" href="login.php">Login</a>
                <a class="btn btn-primary rounded-pill px-4 ms-2" href="signup.php">Sign Up</a>
            </div>
        </div>
    </div>
</nav>
