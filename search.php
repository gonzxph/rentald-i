<?php
session_start();

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
                            <div class="input-group mb-3 flex-column">
                                <h6 class="mb-1">Pickup Date and Time</h6>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-calendar-alt text-secondary"></i>
                                        </span>
                                        <input readonly id="dateTimeInput" name="dateTimeInput" type="text" 
                                            data-bs-toggle="modal" data-bs-target="#dateTimeModal" 
                                            class="form-control" value="<?= htmlspecialchars($date) ?>" 
                                            placeholder="Choose date and time" required>
                                    </div>
                            </div>
                            <div class="input-group mb-3 flex-column">
                                <h6 class="mb-1">Duration</h6>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-calendar-alt text-secondary"></i>
                                        </span>
                                        <input readonly id="duration" name="duration" type="text" class="form-control" value="<?= isset($durationDay) ? htmlspecialchars($durationDay) : ''; ?> Day(s) <?= isset($durationHour) ? htmlspecialchars($durationHour) : ''; ?> Hour(s)" placeholder="Duration" required>
                                        <input id="durationDay" name="durationDay" type="hidden" value="<?= isset($durationDay) ? htmlspecialchars($durationDay) : ''; ?>">
                                        <input id="durationHour" name="durationHour" type="hidden" value="<?= isset($durationHour) ? htmlspecialchars($durationHour) : ''; ?>">
                                    </div>
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
                                    <img 
                                        class="img-fluid" 
                                        src="upload/car/<?= htmlspecialchars($car['img_url'] ?? 'default.png'); ?>" 
                                        alt="Car Image" 
                                        width="200px"
                                    >
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
                                            <a href="booking.php?carid=<?= htmlspecialchars($car['car_id']); ?>&pickup=<?= htmlspecialchars($start_datetime); ?>&dropoff=<?= htmlspecialchars($end_datetime); ?>&day=<?= htmlspecialchars(urlencode($durationDay)); ?>&hour=<?= htmlspecialchars(urlencode($durationHour)); ?>"><button id="bookBtn" class="btn w-100">BOOK</button></a>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="dateTimeModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Select Your Rental Period
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Calendar Container -->
                    <div class="date-selection-container mb-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Select your pickup and return dates</h6>
                        
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
                        <i class="fas fa-check me-2"></i>Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script src="js/main.js"></script>
    <script src="js/calendar.js"></script>
    <script>
    $(document).ready(function() {
        const $calendarElement = $('#vanillaCalendar');
        let calendar = null;

        // Update time options based on selected date
        function updateTimeOptions() {
            const $pickupTimeSelect = $('#pickupTimeInput');
            const today = new Date();
            const selectedDate = calendar.selectedDates[0];
            
            // Reset all options first
            $pickupTimeSelect.find('option').prop('disabled', false);

            // If selected date is today, disable past times
            if (selectedDate === today.toISOString().split('T')[0]) {
                const currentHour = today.getHours();
                const currentMinutes = today.getMinutes();

                $pickupTimeSelect.find('option').each(function() {
                    const timeStr = $(this).val();
                    if (timeStr) {
                        const [hours, minutes] = timeStr.split(':');
                        let hour = parseInt(hours);
                        
                        // Convert to 24-hour format
                        if (timeStr.includes('PM') && hour !== 12) {
                            hour += 12;
                        } else if (timeStr.includes('AM') && hour === 12) {
                            hour = 0;
                        }

                        // Disable if hour is before current time
                        if (hour < currentHour || (hour === currentHour && parseInt(minutes) <= currentMinutes)) {
                            $(this).prop('disabled', true);
                        }
                    }
                });
            }
        }

        if ($calendarElement.length) {
            calendar = new VanillaCalendar('#vanillaCalendar', {
                settings: {
                    iso8601: false,
                    range: {
                        min: new Date().toISOString().split('T')[0],
                        max: '2031-12-31'
                    },
                    visibility: {
                        monthShort: true,
                        theme: 'light'
                    },
                    selection: {
                        day: 'multiple-ranged',
                    }
                },
                actions: {
                    clickDay(event, self) {
                        console.log("Selected Dates:", self.selectedDates);
                        updateTimeOptions();
                    }
                }
            });
            calendar.init();
            
            updateTimeOptions();
        }

        $('#pconfirm').on('click', function() {
            // Hide all error messages initially
            $('.alert').addClass('d-none');

            const pickupTime = $('#pickupTimeInput').val();
            const dropOffTime = $('#dropOffTimeInput').val();

            // Check if calendar dates are selected
            if (!calendar || calendar.selectedDates.length < 2) {
                $('#CalEmptyErr').removeClass('d-none');
                return;
            }

            // Check if both times are empty
            if (!pickupTime && !dropOffTime) {
                $('#timeEmptyErr').removeClass('d-none');
                return;
            }

            // Check if only one time is filled
            if ((!pickupTime && dropOffTime) || (pickupTime && !dropOffTime)) {
                $('#timeOneFilledErr').removeClass('d-none');
                return;
            }

            // If we get here, all validations passed
            const pickupDate = calendar.selectedDates[0];
            const dropOffDate = calendar.selectedDates[calendar.selectedDates.length - 1];
            
            const pickupDateTime = new Date(`${pickupDate} ${pickupTime}`);
            const dropOffDateTime = new Date(`${dropOffDate} ${dropOffTime}`);

            const diffInMillis = dropOffDateTime - pickupDateTime;

            if (diffInMillis > 0) {
                const options = { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    hour12: true 
                };
                const pickupFormatted = pickupDateTime.toLocaleString('en-US', options);
                const dropOffFormatted = dropOffDateTime.toLocaleString('en-US', options);

                const diffInHours = diffInMillis / (1000 * 60 * 60);
                const days = Math.floor(diffInHours / 24);
                const hours = Math.floor(diffInHours % 24);

                // Update the dateTimeInput with formatted dates
                $('#dateTimeInput').val(`${pickupFormatted} - ${dropOffFormatted}`);
                
                // Update the hidden duration inputs
                $('#durationDay').val(days);
                $('#durationHour').val(hours);
                
                // Update the duration display
                $('#duration').val(`${days} Day(s) ${hours} Hour(s)`);

                $('#dateTimeModal').modal('hide');
            }
        });
    });
    </script>
</body>
</html>