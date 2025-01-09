<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Trend Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="sales_trend_content.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
</head>

<body>
    <div class="container-fluid" style="margin-top:30px;">
        <div class="outer-box">
            <h1 class="text-left mb-4">Sales Trend</h1>
            
            <!-- First Card for Dual Axis Chart -->
            <div class="card">
                <h2>Booking Count and Booking Duration of All the Years</h2>
                <div class="chart-container" style="height: 500px; width:100%;">
                    <canvas id="dualAxisChart"></canvas>
                </div>
                <div class="cluster-info" style="display: flex; justify-content: center; gap: 20px; align-items: center;">
                    <p><strong>Peak Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(255, 99, 132, 0.8); border-radius: 50%;"></span>
                    </p>
                    <p><strong>Mid Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(255, 205, 86, 0.8); border-radius: 50%;"></span>
                    </p>
                    <p><strong>Low Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(54, 162, 235, 0.8); border-radius: 50%;"></span>
                    </p>
                </div>
                
            </div>

            <!-- Second Card for Single Year Chart -->
            <div class="card">
            <h2>Booking Count and Booking Duration Chart for the Selected Year</h2>
            <div class="year-selector">
                    <label for="year">Select Year:</label>
                    <select id="year">
                        <option value="" disabled selected>Select a year</option>
                        <!-- Dropdown options will be populated here -->
                    </select>
                </div>
                
                <div class="chart-container" style="height: 500px;">
                    <canvas id="singleYearChart"></canvas>
                </div>
                <div class="cluster-info" style="display: flex; justify-content: center; gap: 20px; align-items: center;">
                    <p><strong>Peak Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(255, 99, 132, 0.8); border-radius: 50%;"></span>
                    </p>
                    <p><strong>Mid Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(255, 205, 86, 0.8); border-radius: 50%;"></span>
                    </p>
                    <p><strong>Low Season:</strong>  
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: rgba(54, 162, 235, 0.8); border-radius: 50%;"></span>
                    </p>
                </div>
            </div>
                

            <div class="back-btn-container">
                <button type="button" class="btn btn-primary" onclick="goToDashboard()">Back</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Store the chart instance for later destruction
            let dualAxisChartInstance = null;
            let singleYearChartInstance = null;

            // Populate Year Dropdown
            fetchAvailableYears();

            // Fetch All Years Chart Data
            fetchAllYearsChartData();

            // Add Event Listener for Year Dropdown Change
            $('#year').on('change', function () {
                const selectedYear = $(this).val();  // Get the selected year value
                console.log("Selected Year:", selectedYear);  // Log to confirm it's correct
                if (selectedYear) {
                    fetchSingularYearChartData(selectedYear);  // Call function to fetch chart data
                }
            });

            function fetchAvailableYears() {
                $.ajax({
                    url: 'get_available_years.php', // Endpoint for fetching available years
                    method: 'GET',
                    dataType: 'json', // Explicitly expect JSON response
                    success: function (response) {
                        console.log(response); // Log the response to ensure it's correct
                        if (response && response.success && Array.isArray(response.years) && response.years.length > 0) {
                            const yearDropdown = $('#year');
                            yearDropdown.empty(); // Clear existing options
                            yearDropdown.append('<option value="" disabled selected>Select a year</option>');

                            // Append years to dropdown
                            response.years.forEach(year => {
                                yearDropdown.append(`<option value="${year}">${year}</option>`);

                            });
                            const currentYearMinusOne = new Date().getFullYear() - 1;
                            yearDropdown.val(currentYearMinusOne);
                            fetchSingularYearChartData(currentYearMinusOne);
                        } else {
                            console.error('Failed to fetch available years:', response.message || 'No years available');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching available years:', error);
                    }
                });
            }

            function fetchAllYearsChartData() {
                $.ajax({
                    url: 'get_sales_trend_data.php', // Endpoint for all years
                    method: 'GET',
                    success: function (response) {
                        const months = [];
                        const bookings = [];
                        const hours = [];

                        $.ajax({
                            url: 'get_kmean_for_all_years.php', // KMeans for all years
                            method: 'GET',
                            success: function (kmeansData) {
                                const clusters = kmeansData.clusters;

                                response.forEach(item => {
                                    months.push(getMonthName(item.month));
                                    bookings.push(item.normalized_bookings_per_month);
                                    hours.push(item.normalized_hours_per_month);
                                });

                                renderAllYearsChart(months, bookings, hours, clusters);
                            },
                            error: function (xhr, status, error) {
                                console.error("Error fetching KMeans data:", error);
                            }
                        });
                    },
                    error: function (xhr, status, error) {  
                        console.error("Error fetching sales trend data:", error);
                    }
                });
            }

            function fetchSingularYearChartData(year) {
                // Destroy the old chart instance if it exists
                if (singleYearChartInstance) {
                    singleYearChartInstance.destroy();
                }

                $.ajax({
                    url: 'get_singular_year_sales_trend.php',
                    method: 'GET',
                    data: { year: year },
                    success: function (response) {
                        console.log(response);
                        const months = [];
                        const bookings = [];
                        const hours = [];

                        response.forEach(item => {
                            months.push(getMonthName(item.month));
                            bookings.push(item.total_bookings);
                            hours.push(item.total_hours);
                        });

                        $.ajax({
                            url: 'kmean_for_singular_year.php',
                            method: 'GET',
                            data: { year: year },
                            success: function (kmeansData) {
                                const clusters = kmeansData.clusters;
                                console.log(kmeansData);
                                renderSingularYearChart(months, bookings, hours, clusters);
                            },
                            error: function (xhr, status, error) {
                                console.error("Error fetching KMeans data:", error);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching sales trend data:", error);
                    }
                });
            }

            function renderAllYearsChart(months, bookings, hours, clusters) {
                const ctx = document.getElementById('dualAxisChart').getContext('2d');

                // Define cluster colors for easy reuse
                const clusterColors = {
                    'Peak': 'rgba(255, 99, 132, 0.8)',  // Red for Peak
                    'Mid': 'rgba(255, 205, 86, 0.8)',   // Yellow for Mid
                    'Low': 'rgba(54, 162, 235, 0.8)'    // Blue for Low
                };

                // Map the months to their corresponding cluster colors
                const barColors = months.map(month => clusterColors[clusters[month]] || 'rgba(128, 128, 128, 0.5)');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Average Booking Duration Hours(Line)', // Line dataset for hours
                                data: hours,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0.5)', // Line color
                                borderColor: 'rgb(0, 0, 0)',
                                borderWidth: 2,
                                yAxisID: 'y2', // Associate with right y-axis
                                order: 0  // Line in front of the bars
                            },
                            {
                                label: 'Average Bookings(Bar)', // Bar dataset for bookings
                                data: bookings,
                                backgroundColor: barColors, // Dynamic bar colors
                                borderColor: barColors,
                                borderWidth: 1,
                                yAxisID: 'y1', // Associate with left y-axis
                                order: 1 // Bars behind the line
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y1: {
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Bookings'
                                }
                            },
                            y2: {
                                type: 'linear',
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Booking Duration (hours)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        if (tooltipItem.datasetIndex === 0) {
                                            return `Average Booking Duration (hours): ${tooltipItem.raw}`;
                                        } else if (tooltipItem.datasetIndex === 1) {
                                            return `Average Booking: ${tooltipItem.raw}`;
                                        }
                                    },
                                    afterBody: function (tooltipItems) {
                                        const monthName = tooltipItems[0].label;
                                        const cluster = clusters[monthName];
                                        return `Cluster: ${cluster}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }


            function renderSingularYearChart(months, bookings, hours, clusters) {
                const ctx = document.getElementById('singleYearChart').getContext('2d');

                // Define cluster colors for easy reuse
                const clusterColors = {
                    'Peak': 'rgba(255, 99, 132, 0.8)',  // Red for Peak
                    'Mid': 'rgba(255, 205, 86, 0.8)',   // Yellow for Mid
                    'Low': 'rgba(54, 162, 235, 0.8)'    // Blue for Low
                };

                // Map the months to their corresponding cluster colors
                const barColors = months.map(month => clusterColors[clusters[month]] || 'rgba(128, 128, 128, 0.5)');

                singleYearChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Average Booking Duration Hours (Line)', // Line dataset for hours
                                data: hours,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0.5)', // Line color
                                borderColor: 'rgb(0, 0, 0)',
                                borderWidth: 2,
                                yAxisID: 'y2', // Associate with right y-axis
                                order: 0  // Line in front of the bars
                            },
                            {
                                label: 'Average Bookings (Bar)', // Bar dataset for bookings
                                data: bookings,
                                backgroundColor: barColors, // Dynamic bar colors
                                borderColor: barColors,
                                borderWidth: 1,
                                yAxisID: 'y1', // Associate with left y-axis
                                order: 1 // Bars behind the line
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y1: {
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Bookings'
                                }
                            },
                            y2: {
                                type: 'linear',
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Total Hours'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        if (tooltipItem.datasetIndex === 0) {
                                            return `Booking Duration (hours): ${tooltipItem.raw}`;
                                        } else if (tooltipItem.datasetIndex === 1) {
                                            return `Bookings: ${tooltipItem.raw}`;
                                        }
                                    },
                                    afterBody: function (tooltipItems) {
                                        const monthName = tooltipItems[0].label;
                                        const cluster = clusters[monthName];
                                        return `Cluster: ${cluster}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function getMonthName(monthNumber) {
                const months = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                return months[monthNumber - 1];
            }


        });
        function goToDashboard() {
                window.location.href = 'index.php';
            }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
