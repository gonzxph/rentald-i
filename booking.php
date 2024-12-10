<?php

require_once './backend/booking_handler.php';
require_once './backend/search_handler.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB70fmdxTT6eYDICyXwGr7rZDy-0DZJSQY&libraries=places"></script>

    <title><?= htmlspecialchars($car['car_brand']) . " " . htmlspecialchars($car['car_model']); ?></title>
    <style>
        .gallery-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }
        .main-image-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .main-image {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .thumbnail-container {
            position: relative;
            overflow: hidden;
        }
        .thumbnail-wrapper {
            display: flex;
            transition: transform 0.3s ease;
        }
        .thumbnail-item {
            flex: 0 0 auto;
            width: calc(20% - 8px);
            margin-right: 10px;
            background: white;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .thumbnail-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .thumbnail-item.active {
            border: 2px solid #0d6efd;
        }
        .thumbnail {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #0d6efd;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .nav-arrow:hover {
            background: rgba(255, 255, 255, 0.9);
        }
        .nav-arrow.prev {
            left: 10px;
        }
        .nav-arrow.next {
            right: 10px;
        }

        .payment-method .form-check-input {
            display: none;
        }

        .payment-method .form-check-label {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-method .form-check-input:checked + .form-check-label {
            border-color: #198754 !important;
            background-color: #f8f9fa;
        }

     

        .upload-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }

        h6 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #333;
        }

        .upload-button {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .upload-button label {
            background: #0066ff;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s ease;
        }

        .upload-button label:hover {
            background: #0052cc;
        }

        .file-count {
            text-align: center;
            margin-bottom: 1rem;
            color: #666;
        }

        .preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-item {
            position: relative;
        }

        .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .preview-item p {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
            word-break: break-all;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        input[type="file"] {
            display: none;
        }

        .booking-details {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .booking-details .detail-row {
        font-size: 0.95rem;
    }
    
    .booking-details .label {
        color: #666;
        font-weight: 500;
    }
    
    .booking-details .value {
        font-weight: 500;
        text-align: right;
    }
    
    .modal-body h2 {
        font-size: 1.75rem;
        font-weight: 600;
    }
    
    .reservation-fee {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
    }
            


    </style>
</head>
<body> 

    <?php include 'includes/nav.php' ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="gallery-container">
                    <div class="main-image-container">
                        <?php if(!empty($carImages)):?>
                            <img id="mainImage" src="upload/car/<?= htmlspecialchars($carImages[0]);?>" alt="Main car view" class="main-image">
                        <?php else :?>
                            <img id="mainImage" src="upload/car/default.png" alt="No image available" class="main-image">
                        <?php endif;?>
                    </div>
                    
                    <div class="thumbnail-container">
                        <div class="thumbnail-wrapper">
                            <?php if(!empty($carImages)) : ?>
                                <?php foreach($carImages as $index => $image) : ?>
                                    <div class="thumbnail-item <?= $index === 0 ? 'active' : ''; ?>" data-image="upload/car/<?= htmlspecialchars($image); ?>">
                                        <img src="upload/car/<?= htmlspecialchars($image); ?>" alt="Car view <?= $index + 1; ?>" class="thumbnail">
                                    </div>
                                <?php endforeach;?>

                            <?php endif;?>
                        </div>
                        <button class="nav-arrow prev"><span class="mdi mdi-arrow-left"></span></button>
                        <button class="nav-arrow next"><span class="mdi mdi-arrow-right"></span></button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <form action="" method="POST">
                    <div class="booking mt-1">
                        <h5>Booking Details</h5>
                        <div class="details">
                            <div class="mb-4">
                                <!-- Toggle Switch -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="sameLocationToggle" checked>
                                    <label class="form-check-label" for="sameLocationToggle">Return car to another location</label>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-warning"></i></span>
                                    <input readonly id="pickupInput" name="pickupinput" type="text" data-bs-toggle="modal" data-bs-target="#pickupModal" class="form-control" placeholder="Choose pick up location" value="">
                                </div>
                                <div class="input-group mb-3" id="dropoffGroup">
                                    <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                    <input readonly id="dropoffInput" name="dropoffinput" type="text" data-bs-toggle="modal" data-bs-target="#pickupModal" class="form-control" placeholder="Choose drop off location" value="">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="fw-medium">Pick Up Date</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="text-muted"><?= htmlspecialchars($pickup_datetime_formatted ?? 'Not set') ?></div>
                                    </div>
                                </div>

                                <!-- Drop off Details -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="fw-medium">Return Date</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="text-muted"><?= htmlspecialchars($dropoff_datetime_formatted ?? 'Not set') ?></div>
                                    </div>
                                </div>
                                
                                

                            </div>

                            <!-- Payment Options -->
                            <div class="mb-4">
                                <h5 class="mb-3">Payment Options</h5>
                                <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentOption" id="reservation" checked>
                                <label class="form-check-label" for="reservation">
                                    Reservation
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentOption" id="fullPayment">
                                <label class="form-check-label" for="fullPayment">
                                    Full payment
                                </label>
                                </div>
                            </div>

                            <!-- Rental Type -->
                            <div class="mb-4">
                                <h5 class="mb-3">Rental Type</h5>
                                <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="rentalType" id="selfDrive" checked>
                                <label class="form-check-label" for="selfDrive">
                                    Self Drive
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="rentalType" id="withDriver">
                                <label class="form-check-label" for="withDriver">
                                    With Driver
                                </label>
                                </div>
                            </div>

                            <!-- Driver's Information (Initially Hidden) -->
                            <div id="driverInfo" class="d-none mb-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Driver's Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" placeholder="Name">
                                        </div>
                                        <div class="mb-3">
                                            <input type="tel" class="form-control" placeholder="Mobile number">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" placeholder="Driver's License Number">
                                        </div>
                                        <div class="mb-3">
                                            <h6 class="mb-2">Upload Driver's License and 2 valid ID</h6>
                                            <div class="upload-button">
                                                <label for="file-input" class="btn btn-primary btn-sm">
                                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                                    </svg>
                                                    Choose A Photo
                                                </label>
                                                <input type="file" id="file-input" multiple accept="image/*" class="d-none">
                                            </div>
                                            <div class="file-count mt-2 text-muted"></div>
                                            <div class="preview-container mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        Book Now
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="pickupModal" tabindex="-1" aria-labelledby="pickupModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTitle"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Search location</strong></p>
                    <form class="d-flex">
                        <input id="autocomplete" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    </form>

                    <div class="d-flex flex-column gap-0 mt-0">
                        <button class="btn d-flex align-items-center mt-2" onclick="getLocation()">
                            <i class="fa-regular fa-circle-dot me-2"></i>
                            <span>Use my current location</span>
                        </button>
                        <button class="btn d-flex align-items-center" onclick="openMapModal()">
                            <i class="fa-solid fa-map me-2"></i>
                            <span>Set location in map</span>
                        </button>
                        <button class="btn d-flex align-items-center" onclick="pickupGarage()">
                            <i class="fa-solid fa-warehouse me-2"></i>
                            <span>Pickup in garage</span>
                        </button>
                    </div>

                    <!-- Display the location coordinates here -->
                    <div id="locationResult" class="mt-3"></div>

                </div> <!-- Closing modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Select location</button>
                </div> <!-- Closing modal-footer -->

            </div> <!-- Closing modal-content -->
        </div> <!-- Closing modal-dialog -->
    </div> <!-- Closing modal -->

    <!-- Second Modal for Map -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="back-btn" onclick="backToPickupModal()"><i class="fas fa-arrow-left"></i></button>
                    <h1 class="modal-title fs-5 m-3">Set Location on Map</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 450px;"></div>
                    <div id="locationResultMap" class="mt-3"></div> <!-- Display selected address here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="selectMapLocation()">Select Location</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Now Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Booking Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Please double check your trip details below and click Book Now to proceed.</p>
                    
                    <div class="booking-details">
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Vehicle</span>
                            <span class="value"><?= htmlspecialchars($car['car_brand'] . ' ' . $car['car_model']);?></span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Type</span>
                            <span class="value"><?= htmlspecialchars($car['car_type']);?></span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Pickup Date</span>
                            <span class="value"><?= htmlspecialchars($pickup_datetime_formatted ?? 'Not set') ?></span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Return Date</span>
                            <span class="value"><?= htmlspecialchars($dropoff_datetime_formatted ?? 'Not set') ?></span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Duration</span>
                            <span class="value"><?= isset($bookingDurationDay) ? htmlspecialchars($bookingDurationDay) : 'sssss'; ?> Day(s) <?= isset($bookingDurationHour) ? htmlspecialchars($bookingDurationHour) : ''; ?> Hour(s)</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Vehicle Rate</span>
                            <span class="value"><?= htmlspecialchars('PHP ' . number_format($totalRate, 2));?></span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">VAT and other charges</span>
                            <span class="value text-success">+ PHP 531.66</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between border-bottom py-2">
                            <span class="label">Total Rental Fee</span>
                            <span class="value">PHP 2,530.66</span>
                        </div>
                    </div>

                    <div class="reservation-fee text-center mt-4">
                        <h3 class="text-success fs-4 mb-0">PHP 1,130.66</h3>
                        <p class="text-muted mb-1">Reservation Fee to Pay</p>
                        <p class="text-muted small mb-3">VAT Inclusive</p>
                        <h4 class="fs-3 fw-bold mb-0">PHP 1,400.00</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100">Pay Now</button>
                </div> 
            </div>
        </div>
    </div>


</body>
<?php include 'includes/scripts.php' ?>
<script src="js/address.js"></script>
<script src="js/uploader.js"></script>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('mainImage');
            const thumbnailWrapper = document.querySelector('.thumbnail-wrapper');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            const prevButton = document.querySelector('.nav-arrow.prev');
            const nextButton = document.querySelector('.nav-arrow.next');

            //Column 2
            const selfDrive = document.getElementById('selfDrive');
            const withDriver = document.getElementById('withDriver');
            const driverInfo = document.getElementById('driverInfo');
            
            let currentIndex = 0;
            const maxIndex = Math.max(0, thumbnails.length - 5);

            function updateArrows() {
                prevButton.style.display = currentIndex > 0 ? 'flex' : 'none';
                nextButton.style.display = currentIndex < maxIndex ? 'flex' : 'none';
            }

            function scrollThumbnails(direction) {
                currentIndex = Math.max(0, Math.min(currentIndex + direction, maxIndex));
                thumbnailWrapper.style.transform = `translateX(-${currentIndex * 20}%)`;
                updateArrows();
            }

            prevButton.addEventListener('click', () => scrollThumbnails(-1));
            nextButton.addEventListener('click', () => scrollThumbnails(1));

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    mainImage.src = this.getAttribute('data-image');
                });
            });

            //Column 2 function
            function toggleDriverInfo() {
                if (withDriver.checked) {
                driverInfo.classList.remove('d-none');
                } else {
                driverInfo.classList.add('d-none');
                }
            }

            

            // Add event listeners to radio buttons
            selfDrive.addEventListener('change', toggleDriverInfo);
            withDriver.addEventListener('change', toggleDriverInfo);

            updateArrows();




            const toggle = document.getElementById("sameLocationToggle");
            const pickupInput = document.getElementById("pickupInput");
            const dropoffGroup = document.getElementById("dropoffGroup");
            const dropoffInput = document.getElementById("dropoffInput");

            // Function to initialize the state of the drop-off field
            function initializeDropoff() {
                if (!toggle.checked) {
                    dropoffGroup.style.display = "none"; // Hide drop-off input
                    dropoffInput.value = pickupInput.value; // Sync pickup and drop-off values
                } else {
                    dropoffGroup.style.display = "flex"; // Show drop-off input
                }
            }

            // Real-time synchronization of pickup and drop-off values
            function syncDropoffValue() {
                if (!toggle.checked) {
                    dropoffInput.value = pickupInput.value; // Sync values dynamically
                }
            }

            // Initialize drop-off input on page load
            initializeDropoff();

            // Listen for changes in the toggle switch
            toggle.addEventListener("change", () => {
                initializeDropoff();
            });

            // Update drop-off value dynamically if pickup changes while toggle is OFF
            pickupInput.addEventListener("input", syncDropoffValue);

            submitButton.onclick = () => {
                
                // Alert the values for testing
                alert(`Pickup Location: ${pickupInput.value}\nDrop-off Location: ${dropoffInput.value}`);
            };
            



        });

        function processPayment() {
                const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
                
                if (!selectedMethod) {
                    alert('Please select a payment method');
                    return;
                }

                // Redirect based on payment method
                switch(selectedMethod.value) {
                    case 'gcash':
                    window.location.href = '/gcash-payment';
                    break;
                    case 'card':
                    window.location.href = '/card-payment';
                    break;
                    case 'paymaya':
                    window.location.href = '/paymaya-payment';
                    break;
                }
        }

        
    </script>
</html>