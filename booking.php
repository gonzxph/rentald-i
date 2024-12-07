<?php

require_once './backend/booking_handler.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>

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
                <div class="booking mt-1">
                    <h5>Booking Details</h5>
                    <div class="details">
                        <div class="mb-4">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="text-muted">Pick up Date & Time</label>
                                    <div class="fw-medium">October 13, 2024 at 08:00 PM</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="text-muted">Pick up Address</label>
                                    <div class="fw-medium">Villareal, Bayawan, 6220, Negros Oriental</div>
                                </div>
                                </div>

                                <!-- Drop off Details -->
                                <div class="row">
                                <div class="col-md-4">
                                    <label class="text-muted">Drop off Date & Time</label>
                                    <div class="fw-medium">October 15, 2024 at 08:30 PM</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="text-muted">Drop off Address</label>
                                    <div class="fw-medium">Pajac, Maribago Road, Lapu-Lapu City 6015</div>
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
                        <div id="driverInfo" class="d-none">
                            <h5 class="mb-3">Driver's Information</h5>
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
                            <input type="file" class="form-control" accept="image/*" placeholder="Driver's License Front Photo">
                            </div>
                            <div class="mb-3">
                            <input type="file" class="form-control" accept="image/*" placeholder="Any Valid ID">
                            </div>
                        </div>

                        <h5 class="card-title mb-4">Price breakdown</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                    <td>Rental charge:</td>
                                    <td class="text-end">₱3,000.00</td>
                                    </tr>
                                    <tr>
                                    <td>Pickup location cost:</td>
                                    <td class="text-end">₱200.00</td>
                                    </tr>
                                    <tr>
                                    <td>Drop-off location cost:</td>
                                    <td class="text-end">₱200.00</td>
                                    </tr>
                                    <tr>
                                    <td>Reservation Fee (Required to secure booking):</td>
                                    <td class="text-end">₱500.00</td>
                                    </tr>
                                    <tr class="fw-bold">
                                    <td>Amount Due at Pickup:</td>
                                    <td class="text-end">₱2,900.00</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>

                            <!-- Payment Method -->
                            <h5 class="mb-3">Payment Method</h5>
                            <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
                                <div class="col">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="gcash" value="gcash">
                                    <label class="form-check-label p-2 border rounded text-center w-100" for="gcash">
                                    <img src="images/icons/gcash-logo.svg" alt="GCash" height="40">
                                    </label>
                                </div>
                                </div>
                                <div class="col">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="card" value="card">
                                    <label class="form-check-label p-2 border rounded text-center w-100" for="card">
                                    <img src="images/icons/master.svg" alt="Visa/Mastercard" height="40">
                                    </label>
                                </div>
                                </div>
                                <div class="col">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paymaya" value="paymaya">
                                    <label class="form-check-label p-2 border rounded text-center w-100" for="paymaya">
                                    <img src="images/icons/maya-logo.svg" alt="PayMaya" height="40">
                                    </label>
                                </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-light w-100" onclick="history.back()">Back</button>
                                <button type="button" class="btn btn-success w-100" onclick="processPayment()">Pay</button>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
<?php include 'includes/scripts.php' ?>
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