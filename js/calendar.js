document.addEventListener("DOMContentLoaded", function () {
    const calendarElement = document.querySelector('#vanillaCalendar');
    let calendar = null;

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
    } else {
        console.error("Calendar element not found.");
    }

    document.getElementById('pconfirm').addEventListener('click', function () {
        if (calendar && calendar.selectedDates.length >= 2) {
            const pickupDate = calendar.selectedDates[0];
            const dropOffDate = calendar.selectedDates[calendar.selectedDates.length - 1];
        
            const pickupTime = document.getElementById('pickupTimeInput').value;
            const dropOffTime = document.getElementById('dropOffTimeInput').value;
        
            if (pickupDate && dropOffDate && pickupTime && dropOffTime) {
                const pickupDateTime = `${new Date(pickupDate).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' })}, ${pickupTime}`;
                const dropOffDateTime = `${new Date(dropOffDate).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' })}, ${dropOffTime}`;
        
                const dateTimeDisplay = `${pickupDateTime} - ${dropOffDateTime}`;
                
                // Set the formatted date and time in the "Choose date and time" input
                const dateTimeInput = document.querySelector('input[placeholder="Choose date and time"]');
                if (dateTimeInput) {
                    dateTimeInput.value = dateTimeDisplay;
                }
        
                // Close the modal
                const dateTimeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('dateTimeModal'));
                dateTimeModal.hide();
            } else {
                document.getElementById('timeOneFilledErr').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('timeOneFilledErr').style.display = 'none';
                }, 5000);
            }
        } else {
            document.getElementById('CalEmptyErr').style.display = 'block';
            setTimeout(() => {
                document.getElementById('CalEmptyErr').style.display = 'none';
            }, 5000);
        }
    });
});

