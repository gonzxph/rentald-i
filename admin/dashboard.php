<!DOCTYPE html>
<html lang="en">
<head>
    <?php

        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';  
    ?>
    <title>D&I CEBU CAR RENTAL</title>

  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="wrapper d-flex flex-column min-vh-100">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Section -->
            <aside class="col-12 col-sm-3 col-md-2 col-lg-2 sidebar" aria-label="Sidebar">
                <!-- Close Button for the sidebar in mobile view -->
                <button class="close-sidebar" id="closeSidebarButton" onclick="toggleSidebar()">&#10005;</button>
                <ul class="list-unstyled">
                    <li onclick="loadContent('dashboard_content.php')" id="dashboard" class="sidebar-item" aria-label="Dashboard">
                        <img src="admin_dashboard_pics/dashboard.png" alt="Speedometer Icon" class="sidebar-icon">
                        Dashboard
                    </li>
                    <li onclick="loadContent('add_vehicle_content.php')" id="add-vehicle" class="sidebar-item" aria-label="Add Vehicle">
                        <img src="admin_dashboard_pics/add_vehicle.png" alt="Car Icon" class="sidebar-icon">
                        Add Vehicle
                    </li>
                    <li onclick="loadContent('sales_trend_content.php')" id="sales" class="sidebar-item" aria-label="Sales/Sales Trend">
                        <img src="admin_dashboard_pics/sales.png" alt="Bar Chart Icon" class="sidebar-icon">
                        Sales/Sales Trend
                    </li>
                    <li onclick="loadContent('booking_content.php')" id="booking-review" class="sidebar-item" aria-label="Booking Review">
                        <img src="admin_dashboard_pics/booking_review.png" alt="Checklist Icon" class="sidebar-icon">
                        Booking Review
                    </li>
                    <li onclick="loadContent('approved_content.php')" id="approved" class="sidebar-item" aria-label="Approved">
                        <img src="admin_dashboard_pics/approved.png" alt="Checkmark Icon" class="sidebar-icon">
                        Approval Status
                    </li>
                </ul>
            </aside>

            <!-- Hamburger Icon -->
            <div class="hamburger-icon d-lg-none" onclick="toggleSidebar()">
                <span>&#9776; Menu</span>
            </div>

            <!-- Main content -->
            <main class="col main-content" id="main-content">
                <?php include 'dashboard_content.php'; ?>
            </main>
        </div>
    </div>

    <!-- Footer Section -->
    <?php include '../footer/footer.php'; ?>
</div>

<!-- JavaScript to handle sidebar toggling and dynamic content loading -->
<script>
    
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const closeButton = document.getElementById('closeSidebarButton');

    // Toggle the 'active' class for the sidebar
    sidebar.classList.toggle('active');

    // Show or hide the close button based on sidebar state
    if (sidebar.classList.contains('active')) {
        closeButton.style.display = 'block'; // Show close button
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    } else {
        closeButton.style.display = 'none'; // Hide close button
        document.body.style.overflow = 'auto'; // Enable scrolling
    }
}

// Dynamically loads the selected content into the main-content area
function loadContent(page) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", page, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("main-content").innerHTML = xhr.responseText;

            // Highlight the selected sidebar item
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                item.classList.remove('selected');
            });
            const clickedItem = Array.from(sidebarItems).find(item => 
                item.getAttribute('onclick') && item.getAttribute('onclick').includes(page)
            );
            if (clickedItem) {
                clickedItem.classList.add('selected');
            }
        } else {
            console.error("Failed to load content:", xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error("An error occurred during the XMLHttpRequest.");
    };
    xhr.send();
}

// For redirecting to Add vehicle content of back button in view car info
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const content = urlParams.get('content'); // Get the 'content' parameter

    if (content) {
        // Load specified content if provided
        loadContent(content);
    } else {
        // Default to the dashboard content
        loadContent('dashboard_content.php');
    }

    // Ensures the Dashboard link is highlighted on initial load
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(item => {
        item.classList.remove('selected');
    });
    const defaultItem = document.querySelector('.sidebar-item[onclick*="dashboard_content.php"]');
    if (defaultItem) {
        defaultItem.classList.add('selected');
    }
});

</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
