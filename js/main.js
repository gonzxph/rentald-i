document.addEventListener("DOMContentLoaded", function(){


            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

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




            function updatePaymentOptionValue() {
                if (reservationRadio.checked) {
                    paymentOptionInput.value = 'reservation';
                } else if (fullPaymentRadio.checked) {
                    paymentOptionInput.value = 'fullPayment';
                }
            }

            
           
            

           


            updateArrows();

            

            const reservationRadio = document.getElementById('reservation');
            const fullPaymentRadio = document.getElementById('fullPayment');
            const paymentOptionInput = document.getElementById('paymentOption');

            const toggle = document.getElementById("sameLocationToggle");
            const pickupInput = document.getElementById("pickupInput");
            const dropoffGroup = document.getElementById("dropoffGroup");
            const dropoffInput = document.getElementById("dropoffInput");
            const submitButton = document.getElementById("submitButton");
            const DeliverySpan = document.getElementById("DeliverySpan");
            const DeliveryFeeInput = document.getElementById("DeliveryFeeInput");
            const pickupSpan = document.getElementById("pickupSpan");
            const PickupFeeInput = document.getElementById("PickupFeeInput");
            const deliveryPickupFee = 300;
            const mcllGraveyardFee = 100;
            const ccGraveyardFee = 50;
            var deliveryFee = 0;
            var pickupFee = 0;

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



            const pickupTime = $("#pickupTimeHiddenInput").val();
            const dropoffTime = $("#dropoffTimeHiddenInput").val();
            

            // Convert time to integer for easier comparison
            const isNightTime = (time) => {
                const [hour, minutes] = time.split(':').map(Number);
                return (hour > 21 || (hour === 21 && minutes >= 0)) || // After 9:00 PM
                       (hour < 6 || (hour === 6 && minutes <= 30));   // Before 6:30 AM
            };

            

            submitButton.onclick = () => {
                // Create FormData from the file input
                const fileInput = document.getElementById('file-input');
                const files = fileInput.files;
                
                // Get the form that submits to booking_cal_handler.php
                const mainForm = document.querySelector('form[action="./backend/booking_cal_handler.php"]');
                
                // Create a single hidden input field for all files
                if (selectedFiles.length > 0) {
                    const formData = new FormData();
                    selectedFiles.forEach((file, index) => {
                        formData.append('file-input[]', file);
                    });
                    
                    // Create a new input for each file
                    selectedFiles.forEach(file => {
                        const fileField = document.createElement('input');
                        fileField.type = 'file';
                        fileField.name = 'file-input[]';
                        fileField.style.display = 'none';
                        
                        // Create a DataTransfer object for this file
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileField.files = dataTransfer.files;
                        
                        mainForm.appendChild(fileField);
                    });
                }

                if(selfDrive.checked){
                    driverName.value = nameInput.value;
                    driverPhone.value = phoneInput.value;
                    driverLicense.value = licenseInput.value;
                }

                if (!toggle.checked) {
                    dropoffInput.value = pickupInput.value; // Sync values
                }

                reservationRadio.addEventListener('change', updatePaymentOptionValue);
                fullPaymentRadio.addEventListener('change', updatePaymentOptionValue);

                // Set initial value
                updatePaymentOptionValue();
                
                
                const pickupInput1 = pickupInput.value;
                const dropoffInput1 = dropoffInput.value;

                $('#pickupAddress').val(pickupInput1);
                $('#pickupAddressText').text(pickupInput1);
                $('#returnAddress').val(dropoffInput1);
                $('#returnAddressText').text(dropoffInput1);



                if (pickupInput1 === "Guada Plains Guadalupe 6000 Cebu City, Philippines") {
                    // If it's the garage, set the delivery fee to 0
                    deliveryFee = 0;
                    DeliverySpan.textContent = "PHP " + deliveryFee;
                    DeliveryFeeInput.value = deliveryFee;
                } else if (pickupInput1.includes("Mandaue") || pickupInput1.includes("Lapu-Lapu")) {
                    if (isNightTime(pickupTime)) {
                        // Apply graveyard fee
                        deliveryFee = deliveryPickupFee + mcllGraveyardFee;
                        DeliverySpan.textContent = "PHP " + deliveryFee;
                        DeliveryFeeInput.value = deliveryFee;
                    } else {
                        // Only apply the regular fee
                        deliveryFee = deliveryPickupFee;
                        DeliverySpan.textContent = "PHP " + deliveryFee;
                        DeliveryFeeInput.value = deliveryFee;
                    }
                }else if(pickupInput1.includes("Cebu City")){
                    deliveryFee = deliveryPickupFee + ccGraveyardFee;
                    DeliverySpan.textContent = "PHP " + deliveryFee;
                    DeliveryFeeInput.value = deliveryFee;
                }

                if (dropoffInput1 === "Guada Plains Guadalupe 6000 Cebu City, Philippines") {
                    // If it's the garage, set the delivery fee to 0
                    pickupFee = 0;
                    pickupSpan.textContent = "PHP " + pickupFee;
                    PickupFeeInput.value = pickupFee;
                } else if (dropoffInput1.includes("Mandaue") || dropoffInput1.includes("Lapu-Lapu")) {
                    if (isNightTime(dropoffTime)) {
                        // Apply graveyard fee
                        pickupFee = deliveryPickupFee + mcllGraveyardFee;
                        pickupSpan.textContent = "PHP " + pickupFee;
                        PickupFeeInput.value = pickupFee;
                    } else {
                        // Only apply the regular fee
                        pickupFee = deliveryPickupFee;
                        pickupSpan.textContent = "PHP " + pickupFee;
                        PickupFeeInput.value = pickupFee;
                    }
                }else if(dropoffInput1.includes("Cebu City")){
                    pickupFee = deliveryPickupFee + ccGraveyardFee;
                    pickupSpan.textContent = "PHP " + pickupFee;
                    PickupFeeInput.value = pickupFee;
                }

                const totalRentFeeSpan = $('#totalRentFeeSpan');
                const totalRentFeeInput = $('#totalRentFeeInput');

                // Parse the numeric values
                let excessFee = parseFloat($('#excessPay').val()) || 0; // Default to 0 if value is null or empty
                let vehicleRate = parseFloat($('#vehicleRate').val()) || 0;
                let deliveryfee = parseFloat(deliveryFee) || 0;
                let pickupfee = parseFloat(pickupFee) || 0;

                // Calculate the total rent fee
                let totalRentFee = excessFee + vehicleRate + deliveryfee + pickupfee;

                // Update the span and input values
                totalRentFeeSpan.text("PHP " + totalRentFee); // Use jQuery `.text()` for span
                totalRentFeeInput.val(totalRentFee);          // Use `.val()` for input

                const remBalance = $('#remBalance');
                remBalance.text("PHP " + (totalRentFee - 500));
                remBalanceInput.val = totalRentFee - 500;
                
            };


})

function validateForm(){
    /* const pax = $('#pax').val(); */
    const dateTimeInput  = $('#dateTimeInput').val();
    const warningMessage = $('#warningMessage');


    if(!dateTimeInput){
        warningMessage.show();
        return false;
    }
    warningMessage.hide();
    return true;


}
