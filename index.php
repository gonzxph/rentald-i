<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>D & I Cebu Car Rental Testing pre</title>
</head>
<body>
    <?php include 'includes/nav.php' ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert" id="logoutAlert">
            Signed out
        </div>
    <?php endif; ?>

    <section class="booking-section">
        <div class="container-fluid one vh-75">
            <div class="row p-5">
                <div class="col-lg-6 mt-4 mb-3">
                    <h1 class="text-center">Your ON-THE-GO road partner</h1>
                    <p class="text-center">Explore Cebu with reliable, affordable, and quality vehicles.</p>
                </div>
                <div class="col-lg-6 d-flex justify-content-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="card text-center shadow-lg p-4" style="width: 25rem;">
                            <h5 class="card-title mb-3">Find the right car now!</h5>
                            <div class="card-body">
                                <form method="POST" action="./search.php" class="m-3" onsubmit="return validateForm()">
                                    <!-- <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-users"></i></span>
                                        <input id="pax" name="pax" type="number" class="form-control" placeholder="Number of pax" value="">
                                    </div> -->
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-secondary"></i></span>
                                        <input readonly name="dateTimeInput" id="dateTimeInput" type="text" data-bs-toggle="modal" data-bs-target="#dateTimeModal" class="form-control" placeholder="Choose date and time">
                                    </div>
                                    <input id="durationDay" name="durationDay" type="hidden" value="">
                                    <input id="durationHour" name="durationHour" type="hidden" value="">
                                    <div id="warningMessage" class="text-danger m-3" style="display: none;">
                                        Please fill out all fields before submitting!
                                    </div>
                                    <button type="submit" class="btn btn-dark mt-3 w-100">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="m-3">
            <div class="container my-5">
                <div class="row">
                    <?php include 'includes/policies_guidelines.php' ?>
                </div>
            </div>
        </div>

        

        
        <!-- Pickup date and time modal -->
        <div class="modal fade" id="dateTimeModal" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"> <!-- Adjusted modal size and centering -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dateTimeModalLabel">Select Pickup & Drop-off Dates and Times</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Centered Calendar Container for Pick-up Date -->
                        <div id="vanillaCalendar" class="vanilla-calendar calendar-center"></div>
                        
                        <!-- Time Picker -->
                        <div id="timeEmptyErr" class="text-danger mt-2" style="display: none">
                            <p>Please select the Pickup Time or Drop-Off Time input below before setting the time.</p>
                        </div>
                        <div id="timeOneFilledErr" class="text-danger mt-2" style="display: none">
                            <p>Please select both pickup and drop-off dates and times.</p>
                        </div>
                        <div id="CalEmptyErr" class="text-danger mt-2" style="display: none">
                            <p>Please select the pickup and drop-off dates from the calendar.</p>
                        </div>
                        
                        <!-- Time Picker Dropdowns -->
                        <div class="row mt-4">
                            <div class="col">
                                <label for="pickupTimeInput">Pickup Time:</label>
                                <select class="form-select" id="pickupTimeInput" name="pickupTimeInput">
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
                            <div class="col">
                                <label for="dropOffTimeInput">Dropoff Time:</label>
                                <select class="form-select mb-3" id="dropOffTimeInput" name="dropOffTimeInput">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="pconfirm">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> <!-- end of booking-section -->

    


    <!-- Footer wrapper -->
    <div class="footer-wrapper">
        <?php include 'includes/footer.php' ?>
    </div>

    <?php include 'includes/scripts.php' ?>
    
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
    <script>
        // Auto dismiss the logout alert after 3 seconds using jQuery
        $(document).ready(function() {
            setTimeout(function() {
                $('#logoutAlert').fadeOut('slow');
            }, 3000);
        });
    </script>
    <?php endif; ?>
</body>
<script src="js/calendar.js"></script>
<script src="js/main.js"></script>

</html>
