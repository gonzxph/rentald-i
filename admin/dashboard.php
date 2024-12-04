<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';
    ?>
    <title>D&I CEBU CAR RENTAL</title>
    <link rel="stylesheet" href="dashboard.css">
   
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <ul>
                <li onclick="loadContent('dashboard_content.php')">
                    <img src="admin_dashboard_pics/dashboard.png" alt="Speedometer Icon" class="sidebar-icon">
                    Dashboard
                </li>
                <li onclick="loadContent('add_vehicle_content.php')">
                    <img src="admin_dashboard_pics/add_vehicle.png" alt="Car Icon" class="sidebar-icon">
                    Add Vehicle
                </li>
                <li>
                    <img src="admin_dashboard_pics/sales.png" alt="Bar Chart Icon" class="sidebar-icon">
                    Sales/Sales Trend
                </li>
                <li onclick="loadContent('booking_content.php')">
                    <img src="admin_dashboard_pics/booking_review.png" alt="Checklist Icon" class="sidebar-icon">
                    Booking Review
                </li>
                <li>
                    <img src="admin_dashboard_pics/approved.png" alt="Checkmark Icon" class="sidebar-icon">
                    Approved
                </li>
            </ul>
        </aside>

        <!-- Main content area to load different pages dynamically -->
        <main class="main-content" id="main-content">
            <?php include 'dashboard_content.php'; ?> 
        </main>
    </div>

   
        <?php include 'CarRental/footer/footer.php'; ?>
   

    <!-- JavaScript to load content dynamically -->
    <script>
        function loadContent(page) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", page, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("main-content").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
