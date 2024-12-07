<?php

require_once './backend/search_handler.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Search Available Vehicle</title>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container mt-5 vh-100">
        <!-- Search Form -->
        <div class="row">
            <div class="col-lg-4 col-md-4 mb-3">
                <div class="card">
                    <div class="card-titlesf">
                        <h5 class="card-title-text p-3 pb-2">Search and Filter</h5>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm" action="./search.php" method="POST">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-secondary"></i></span>
                                <input readonly id="dateTimeInput" name="dateTimeInput" type="text" data-bs-toggle="modal" data-bs-target="#dateTimeModal" class="form-control" value="<?= htmlspecialchars($date) ?>" placeholder="Choose date and time" required>
                            </div>
                            <h5 class="card-title pb-2">Filter</h5>
                            <div class="mb-3">
                                <h6 class="mb-2">VEHICLE:</h6>
                                <?php
                                $vehicle_options = ['Sedan', 'SUV', 'MPV', 'Pickup', 'Van', 'Hatchback', 'L300'];
                                foreach ($vehicle_options as $option) {
                                    $checked = in_array($option, $vehicle_types) ? 'checked' : '';
                                    echo "<div class='form-check'>
                                            <input class='form-check-input' type='checkbox' id='" . strtolower($option) . "' name='vehicle[]' value='$option' $checked>
                                            <label class='form-check-label' for='" . strtolower($option) . "'>$option</label>
                                        </div>";
                                }
                                ?>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-2">TRANSMISSION:</h6>
                                <?php
                                $transmission_options = ['Manual', 'Automatic'];
                                foreach ($transmission_options as $option) {
                                    $checked = in_array($option, $transmission_types) ? 'checked' : '';
                                    echo "<div class='form-check'>
                                            <input class='form-check-input' type='checkbox' id='" . strtolower($option) . "' name='transmission[]' value='$option' $checked>
                                            <label class='form-check-label' for='" . strtolower($option) . "'>$option</label>
                                        </div>";
                                }
                                ?>
                            </div>
                            <button type="submit" class="btn btn-dark mt-3 w-100">Search</button>
                        </form>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mt-3">
                                <?= htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-8 col-md-8">
                <?php if (!empty($available_cars)): ?>
                    <?php foreach ($available_cars as $car): ?>
                        <div class="d-flex justify-content-between p-3 border mb-3 rounded">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <img class="img-fluid" src="#" alt="" width="200px">
                                    </div>
                                    <div class="col-lg-6 col-6">
                                        <h5 class="font-weight-medium"><?= htmlspecialchars($car['car_brand']) . " " . htmlspecialchars($car['car_model']); ?></h5>
                                        <span class="mdi mdi-car"> <?= htmlspecialchars($car['car_type']); ?></span>
                                        <span class="mdi mdi-cog"> <?= htmlspecialchars($car['car_transmission_type']); ?></span>
                                        <span class="mdi mdi-car-seat"> <?= htmlspecialchars($car['car_seats']); ?> Seats</span>
                                    </div>
                                    <div class="col-lg-3 col-12 mt-5">
                                        <div class="text-end">
                                            <p class="mb-4"><strong>₱<?= number_format($car['car_rental_rate'], 2); ?></strong></p>
                                            <button id="viewDetailBtn" class="btn mb-2 w-100">VIEW DETAILS</button>
                                            <a href="booking.php?carid=<?= htmlspecialchars($car['car_id']); ?>"><button id="bookBtn" class="btn w-100">BOOK</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&dateTimeInput=<?= urlencode($date) ?>&<?= http_build_query(['vehicle' => $vehicle_types, 'transmission' => $transmission_types]) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">No cars available for the selected criteria.</div>
                <?php endif; ?>
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
                    <div class="row mt-4">
                        <div class="col">
                            <label for="pickupTimeInput">Pickup Time:</label>
                            <input type="text" class="form-control" id="pickupTimeInput" readonly value="">
                        </div>
                        <div class="col">
                            <label for="dropOffTimeInput">Dropoff Time:</label>
                            <input type="text" class="form-control mb-3" id="dropOffTimeInput" readonly value="">
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="pconfirm">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script src="js/main.js"></script>
    <script src="js/calendar.js"></script>
</body>
</html>