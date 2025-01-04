<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'index.php' : 'index.php'; ?>">
            <img src="images/logo/logo1.png" alt="Stratlab Logo" height="40">
        </a>

        <!-- Desktop Navigation Items -->
        <div class="d-none d-lg-flex ms-4">
            <ul class="navbar-nav me-auto mb-0">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-map-marked-alt me-1"></i>Dashboard
                        </a>
                    </li>
                <?php endif; ?>
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
                            <img src="<?php 
                                echo isset($_SESSION['profile_image']) && $_SESSION['profile_image'] 
                                    ? htmlspecialchars($_SESSION['profile_image']) 
                                    : 'images/profile/user.png'; ?>" 
                                 alt="Profile" 
                                 class="rounded-circle" 
                                 width="35" 
                                 height="35"
                                 onerror="this.src='images/profile/user.png';">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown">
                            <!-- User Info Header -->
                            <li>
                                <div class="px-3 py-2">
                                    <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['fname'] . ' ' . $_SESSION['lname']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            <!-- Main Actions -->
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-columns me-2"></i>Dashboard</a></li>
                            
                            <!-- Rental Section -->
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Rentals</h6></li>
                            <li><a class="dropdown-item" href="my-bookings.php"><i class="fas fa-calendar-check me-2"></i>Active Rentals</a></li>
                            <li><a class="dropdown-item" href="my-bookings.php?status=upcoming"><i class="fas fa-clock me-2"></i>Upcoming Bookings</a></li>
                            <li><a class="dropdown-item" href="my-bookings.php?status=history"><i class="fas fa-history me-2"></i>Rental History</a></li>

                            <!-- Account Settings -->
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Account</h6></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item" href="verifications.php"><i class="fas fa-shield-alt me-2"></i>Verification Status</a></li>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-user-shield me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>

                            <!-- Help & Support -->
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Support</h6></li>
                            <li><a class="dropdown-item" href="support.php"><i class="fas fa-question-circle me-2"></i>Help Center</a></li>
                            <li><a class="dropdown-item" href="contact.php"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                            
                            <!-- Sign Out -->
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="signout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                                </a>
                            </li>
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
