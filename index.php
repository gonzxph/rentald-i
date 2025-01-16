<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>D & I Cebu Car Rentald</title>
</head>
<body>
    <?php include 'includes/nav.php' ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="alert alert-warning fade show d-flex justify-content-center" role="alert" id="logoutAlert">
            Signed out
        </div>
    <?php endif; ?>

    <section class="booking-section min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mt-4 mb-3">
                    <h1 class="display-5 fw-bold">Your ON-THE-GO road partner</h1>
                    <p class="lead">Explore Cebu with reliable, affordable, and quality vehicles. Experience hassle-free car rental with our premium service.</p>
                    <div class="d-flex gap-4 mt-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>
                            <span>Fully insured</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <span>24/7 support</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <span>Island-wide service</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-flex justify-content-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="card text-center shadow-lg p-4" style="width: 25rem;">
                            <h5 class="card-title mb-3">Find the right car now!</h5>
                            <div class="card-body">
                                <form method="POST" action="./search.php" class="m-3" onsubmit="return validateForm()">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-secondary"></i></span>
                                        <input readonly name="dateTimeInput" id="dateTimeInput" type="text" data-bs-toggle="modal" data-bs-target="#dateTimeModal" class="form-control" placeholder="Choose date and time">
                                    </div>
                                    <input id="durationDay" name="durationDay" type="hidden" value="">
                                    <input id="durationHour" name="durationHour" type="hidden" value="">
                                    <button type="submit" class="btn btn-primary mt-3 w-100" id="searchCarsBtn">Search Available Cars</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pickup date and time modal -->
    <div class="modal fade" id="dateTimeModal" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="dateTimeModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Select Your Rental Period
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Enhanced Error Messages -->
                    <div class="mb-3">
                        <div class="alert alert-warning d-none fade show" id="timeEmptyErr" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Almost there!</strong> Please select your preferred pickup and return times to continue.
                        </div>
                        <div class="alert alert-warning d-none fade show" id="timeOneFilledErr" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>One more step!</strong> Please make sure to select both pickup and return times.
                        </div>
                        <div class="alert alert-warning d-none fade show" id="CalEmptyErr" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Quick reminder:</strong> Please select both your pickup and return dates on the calendar below.
                        </div>
                    </div>

                    <!-- Calendar Container -->
                    <div class="date-selection-container mb-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Select your pickup and return dates</h6>
                        <div id="vanillaCalendar" class="vanilla-calendar calendar-center"></div>
                    </div>
                    
                    <!-- Time Selection -->
                    <div class="time-selection-container">
                        <h6 class="text-muted mb-3"><i class="far fa-clock me-2"></i>Select your pickup and return times</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pickupTimeInput" class="form-label">Pickup Time</label>
                                <select class="form-select form-select-lg" id="pickupTimeInput" name="pickupTimeInput">
                                    <option value="">Select time</option>
                                    <option value="12:00 am">12:00 am</option>
                                    <option value="01:00 am">01:00 am</option>
                                    <option value="02:00 am">02:00 am</option>
                                    <option value="03:00 am">03:00 am</option>
                                    <option value="04:00 am">04:00 am</option>
                                    <option value="05:00 am">05:00 am</option>
                                    <option value="06:00 am">06:00 am</option>
                                    <option value="07:00 am">07:00 am</option>
                                    <option value="08:00 am">08:00 am</option>
                                    <option value="09:00 am">09:00 am</option>
                                    <option value="10:00 am">10:00 am</option>
                                    <option value="11:00 am">11:00 am</option>
                                    <option value="12:00 pm">12:00 pm</option>
                                    <option value="01:00 pm">01:00 pm</option>
                                    <option value="02:00 pm">02:00 pm</option>
                                    <option value="03:00 pm">03:00 pm</option>
                                    <option value="04:00 pm">04:00 pm</option>
                                    <option value="05:00 pm">05:00 pm</option>
                                    <option value="06:00 pm">06:00 pm</option>
                                    <option value="07:00 pm">07:00 pm</option>
                                    <option value="08:00 pm">08:00 pm</option>
                                    <option value="09:00 pm">09:00 pm</option>
                                    <option value="10:00 pm">10:00 pm</option>
                                    <option value="11:00 pm">11:00 pm</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dropOffTimeInput" class="form-label">Return Time</label>
                                <select class="form-select form-select-lg" id="dropOffTimeInput" name="dropOffTimeInput">
                                    <option value="">Select time</option>
                                    <option value="12:00 am">12:00 am</option>
                                    <option value="01:00 am">01:00 am</option>
                                    <option value="02:00 am">02:00 am</option>
                                    <option value="03:00 am">03:00 am</option>
                                    <option value="04:00 am">04:00 am</option>
                                    <option value="05:00 am">05:00 am</option>
                                    <option value="06:00 am">06:00 am</option>
                                    <option value="07:00 am">07:00 am</option>
                                    <option value="08:00 am">08:00 am</option>
                                    <option value="09:00 am">09:00 am</option>
                                    <option value="10:00 am">10:00 am</option>
                                    <option value="11:00 am">11:00 am</option>
                                    <option value="12:00 pm">12:00 pm</option>
                                    <option value="01:00 pm">01:00 pm</option>
                                    <option value="02:00 pm">02:00 pm</option>
                                    <option value="03:00 pm">03:00 pm</option>
                                    <option value="04:00 pm">04:00 pm</option>
                                    <option value="05:00 pm">05:00 pm</option>
                                    <option value="06:00 pm">06:00 pm</option>
                                    <option value="07:00 pm">07:00 pm</option>
                                    <option value="08:00 pm">08:00 pm</option>
                                    <option value="09:00 pm">09:00 pm</option>
                                    <option value="10:00 pm">10:00 pm</option>
                                    <option value="11:00 pm">11:00 pm</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="pconfirm">
                        <i class="fas fa-check me-2"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
     <!-- end of booking-section -->
    <div class="container my-4">
        <div class="row">
            <?php include 'includes/policies_guidelines.php' ?>
        </div>
    </div>
  
    <!-- Footer wrapper -->
    <div class="footer-wrapper">
        <?php include 'includes/footer.php' ?>
    </div>

    <?php include 'includes/scripts.php' ?>
    <script>
        $(document).ready(function() {
            $('#searchCarsBtn').on('click', function(event) {
                const dateTimeInput = $('#dateTimeInput').val();
                if (!dateTimeInput) {
                    event.preventDefault(); // Prevent form submission
                    $('#dateTimeModal').modal('show'); // Open the modal
                }
            });
        });
    </script>
    
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
    <script>
        $(document).ready(function() {
            const $logoutAlert = $("#logoutAlert");
            if ($logoutAlert.length) {
                setTimeout(function() {
                    $logoutAlert.fadeOut("slow", function() {
                        $(this).remove();
                    });
                }, 2000);
            }
        });
    </script>
    <?php endif; ?>
</body>
<script src="js/calendar.js"></script>
<script src="js/main.js"></script>
</html>
