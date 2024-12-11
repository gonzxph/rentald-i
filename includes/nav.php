<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'home.php' : 'index.php'; ?>">
            <img src="images/logo/logo1.png" alt="Stratlab Logo" height="40">
        </a>

        <!-- Desktop Navigation Items -->
        <div class="d-none d-lg-flex ms-4">
            <ul class="navbar-nav me-auto mb-0">
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
        </div>

        <!-- Hamburger menu for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Mobile Navigation and Profile -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Mobile Navigation Items -->
            <ul class="navbar-nav d-lg-none">
                <li class="nav-item">
                    <a class="nav-link" href="vehicles.php">
                        <i class="fas fa-car me-1"></i>Vehicles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tours.php">
                        <i class="fas fa-map-marked-alt me-1"></i>Tours
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">
                        <i class="fas fa-envelope me-1"></i>Contact Us
                    </a>
                </li>
            </ul>

            <!-- Right-aligned buttons -->
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/bg/bg-cclex.jpeg" alt="Profile" class="rounded-circle" width="32" height="32">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a href="profile.php" class="dropdown-item px-3 py-2 text-decoration-none">
                                    <strong><?php echo isset($_SESSION['fname']) ? htmlspecialchars($_SESSION['fname']) : ''; ?> <?php echo isset($_SESSION['lname']) ? htmlspecialchars($_SESSION['lname']) : ''; ?></strong>
                                    <div class="text-muted small">View profile</div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">VEHICLE RENTAL</h6></li>
                            <li><a class="dropdown-item" href="my-bookings.php">My bookings</a></li>
                           
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">ACCOUNT</h6></li>
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                            <li><a class="dropdown-item" href="verifications.php">Verifications</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">HELP</h6></li>
                            <li><a class="dropdown-item" href="support.php">Help & Support</a></li>
                            <li><a class="dropdown-item" href="contact.php">Contact us</a></li>
                            <li><a class="dropdown-item" href="signout.php">Sign out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-primary rounded-pill px-4" href="signin.php">Sign In</a>
                    <a class="btn btn-primary rounded-pill px-4 ms-2" href="signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
