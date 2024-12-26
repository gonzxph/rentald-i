<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';  
    ?>
    <title>D&I CEBU CAR RENTAL</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="view_vehicle.css">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="content-container"> <!-- New wrapper for the content -->
            <!-- Car Information Table -->
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <div class="container">
                            <section class="booking-status">
                                <h2>Booking Status</h2>
                                <p class="status-text">Waiting for approval</p>
                            </section>

                            <section class="pickup-dropoff">
                                <div class="pickup">
                                    <h2>Pick-up Details</h2>
                                    <p>Date & Time: <span>2024-10-15 | 10:00 AM</span></p>
                                    <p>Address: <span>123 Main St, City, Country</span></p>
                                </div>
                                <div class="dropoff">
                                    <h2>Drop-off Details</h2>
                                    <p>Date & Time: <span>2024-10-15 | 10:00 AM</span></p>
                                    <p>Address: <span>123 Main St, City, Country</span></p>
                                </div>
                            </section>

                            <section class="payment-option">
                                <h2>Payment Option</h2>
                                <p>Method: <span>Full Payment</span></p>
                            </section>

                            <section class="rental-type">
                                <h2>Rental Type</h2>
                                <p>Type: <span>Self Drive</span></p>
                            </section>

                            <section class="car-information">
                                <img src="car-image.jpg" alt="Car Image" class="car-image">
                                <div class="car-details">
                                    <p>Model: <span>Toyota Corolla</span></p>
                                    <p>Year: <span>2021</span></p>
                                </div>
                            </section>

                            <section class="driver-information">
                                <h2>Driver Information</h2>
                                <p>Full Name: <span>John Doe</span></p>
                                <p>Contact Number: <span>+1234567890</span></p>
                                <p>Email: <span>john.doe@example.com</span></p>
                                <p>Driver's License Number: <span>D12345678</span></p>
                                <img src="drivers-license.jpg" alt="Driver's License" class="drivers-license">
                            </section>

                            <section class="price-breakdown">
                                <h2>Price Breakdown</h2>
                                <p>Total Amount Due: <span>PHP 5,140.00</span></p>
                                <p>Amount Paid (Reservation): <span>PHP 500.00</span></p>
                                <p>Balance Due at Pickup: <span>PHP 4,640.00</span></p>
                            </section>

                            <section class="action-buttons">
    <!-- Back Button -->
    <button class="btn btn-secondary back-button" onclick="goToAddVehicle()">Back</button>

    <!-- Decline and Approve Buttons -->
    <button class="btn btn-danger decline-button">Decline</button>
    <button class="btn btn-primary approve-button">Approve</button>
</section>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to redirect back to Add Vehicle Dashboard sidebar-->
    <script>
        function goToAddVehicle() {
            window.location.href = 'dashboard.php?content=add_vehicle_content.php';
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
