document.addEventListener("DOMContentLoaded", function () {
    const calendarElement = document.querySelector('#vanillaCalendar');
    let calendar = null;

    // Add this function to update time options based on selected date
    function updateTimeOptions() {
        const pickupTimeSelect = document.getElementById('pickupTimeInput');
        const today = new Date();
        const selectedDate = calendar.selectedDates[0];
        
        // Reset all options first
        Array.from(pickupTimeSelect.options).forEach(option => {
            option.disabled = false;
        });

        // If selected date is today, disable past times
        if (selectedDate === today.toISOString().split('T')[0]) {
            const currentHour = today.getHours();
            const currentMinutes = today.getMinutes();

            Array.from(pickupTimeSelect.options).forEach(option => {
                if (option.value) {
                    const timeStr = option.value;
                    const [hours, minutes] = timeStr.split(':');
                    let hour = parseInt(hours);
                    
                    // Convert to 24-hour format
                    if (timeStr.includes('pm') && hour !== 12) {
                        hour += 12;
                    } else if (timeStr.includes('am') && hour === 12) {
                        hour = 0;
                    }

                    // Disable if hour is before current time
                    if (hour < currentHour || (hour === currentHour && parseInt(minutes) <= currentMinutes)) {
                        option.disabled = true;
                    }
                }
            });
        }
    }

    if (calendarElement) {
        calendar = new VanillaCalendar('#vanillaCalendar', {
            settings: {
                iso8601: false,
                range: {
                    min: new Date().toISOString().split('T')[0], // Set the minimum date to today
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
                },
                clickMonth: (month, year) => {
                    const minYear = new Date().getFullYear();
                    const maxYear = 2030;
                    if (year < minYear) {
                        calendar.setDate(`${minYear}-01-01`);
                    } else if (year > maxYear) {
                        calendar.setDate(`${maxYear}-12-31`);
                    }
                }
            }
        });
        calendar.init();
        
        // Initial update of time options
        updateTimeOptions();
    } else {
        console.error("Calendar element not found.");
    }

    document.getElementById('pconfirm').addEventListener('click', function () {
        if (calendar && calendar.selectedDates.length >= 2) {
            const pickupDate = calendar.selectedDates[0];
            const dropOffDate = calendar.selectedDates[calendar.selectedDates.length - 1];

            const pickupTime = document.getElementById('pickupTimeInput').value;
            const dropOffTime = document.getElementById('dropOffTimeInput').value;

            if (pickupTime && dropOffTime) {
                // Combine date and time to create Date objects
                const pickupDateTime = new Date(`${pickupDate} ${pickupTime}`);
                const dropOffDateTime = new Date(`${dropOffDate} ${dropOffTime}`);

                // Calculate the difference in milliseconds
                const diffInMillis = dropOffDateTime - pickupDateTime;

                if (diffInMillis > 0) {
                    // Format the dates
                    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
                    const pickupFormatted = pickupDateTime.toLocaleString('en-US', options);
                    const dropOffFormatted = dropOffDateTime.toLocaleString('en-US', options);

                    // Calculate the days and hours difference
                    const diffInHours = diffInMillis / (1000 * 60 * 60);
                    const days = Math.floor(diffInHours / 24);
                    const hours = Math.floor(diffInHours % 24);

                    // Display the result
                    document.getElementById('dateTimeInput').value = `${pickupFormatted} - ${dropOffFormatted}`;
                    document.getElementById('durationDay').value = days;
                    document.getElementById('durationHour').value = hours;

                    // Optionally close the modal
                    $('#dateTimeModal').modal('hide');
                }
            }
        }
    });
});