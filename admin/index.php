<?php
session_start();
if($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['logged_in'] !== true){
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include 'admin_header/admin_header.php';
        include 'admin_header/admin_nav.php';  
    ?>
    <title>D&I CEBU CAR RENTAL</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
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
                    <li onclick="loadContent('booking_content.php')" id="booking-review" class="sidebar-item" aria-label="Booking Review">
                        <img src="admin_dashboard_pics/booking_review.png" alt="Checklist Icon" class="sidebar-icon">
                        Booking Review
                    </li>
                    <li onclick="loadContent('approved_content.php')" id="approved" class="sidebar-item" aria-label="Approved">
                        <img src="admin_dashboard_pics/approved.png" alt="Checkmark Icon" class="sidebar-icon">
                        Approved List
                    </li>
                    <li onclick="loadContent('rejected_content.php')" id="rejected" class="sidebar-item" aria-label="Rejected">
                        <img src="admin_dashboard_pics/reject.png" alt="Cross Icon" class="sidebar-icon">
                        Rejected List
                    </li>
                    <li id="sales" class="sidebar-item" aria-label="Sales/Sales Trend">
                        <a href="sales_trend_content.php" class="sidebar-link">
                            <img src="admin_dashboard_pics/sales.png" alt="Bar Chart Icon" class="sidebar-icon">
                            Sales/Sales Trend
                        </a>
                    </li>
                    <li class="sidebar-item" aria-label="Logout">
                        <a href="logout.php" class="sidebar-link logout-link">
                            <img src="admin_dashboard_pics/log-out.svg" alt="Logout Icon" class="sidebar-icon">
                            Logout
                        </a>
                    </li>
                    <style>.sidebar-link {
                            text-decoration: none; 
                            color: inherit; 
                        }

                        .sidebar-link:hover {
                            color: inherit; 
                            text-decoration: none; 
                        }
                        </style>


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
    <?php include 'CarRental/footer/footer.php'; ?>
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
            sidebarItems.forEach(item => item.classList.remove('selected'));

            const clickedItem = Array.from(sidebarItems).find(item =>
                item.getAttribute('onclick') && item.getAttribute('onclick').includes(page)
            );
            if (clickedItem) clickedItem.classList.add('selected');

            // Initialize file upload functionality if we're on add_vehicle_content.php
            if (page === 'add_vehicle_content.php') {
                initializeFileUpload();
            } else if (page.includes('view_car.php')) {
                initializeFileUpload();
            }

            // Add dynamic search functionality for the search bar in approved_content.php if it exists
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const filter = searchInput.value.toLowerCase();
                    const table = document.getElementById('approvedTable');
                    const rows = table.getElementsByTagName('tr');

                    for (let i = 1; i < rows.length; i++) {
                        const cells = rows[i].getElementsByTagName('td');
                        const firstName = cells[1]?.textContent.toLowerCase() || '';
                        const lastName = cells[2]?.textContent.toLowerCase() || '';

                        if (firstName.includes(filter) || lastName.includes(filter)) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                });
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

// Add this function to initialize the file upload functionality
function initializeFileUpload() {
    const fileInput = document.getElementById('image_upload');
    const previewContainer = document.querySelector('.preview-container');
    const fileCount = document.querySelector('.file-count');
    let fileList = new DataTransfer();

    if (!fileInput || !previewContainer || !fileCount) return;

    // Handle file input changes
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        let loadedFiles = 0; // Counter for loaded files
        
        files.forEach(file => {
            fileList.items.add(file);
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <p>${file.name}</p>
                    <button type="button" class="remove-btn" data-name="${file.name}">×</button>
                `;
                
                previewContainer.appendChild(previewItem);

                // Add click handler for the new remove button
                const removeBtn = previewItem.querySelector('.remove-btn');
                removeBtn.addEventListener('click', function() {
                    previewItem.remove();
                    
                    // Update fileList
                    const newFileList = new DataTransfer();
                    const currentFiles = fileList.files;
                    for (let i = 0; i < currentFiles.length; i++) {
                        if (currentFiles[i].name !== file.name) {
                            newFileList.items.add(currentFiles[i]);
                        }
                    }
                    fileList = newFileList;
                    fileInput.files = fileList.files;
                    updateFileCount(fileInput);
                });

                // Increment counter and update count only after file is loaded
                loadedFiles++;
                if (loadedFiles === files.length) {
                    fileInput.files = fileList.files;
                    updateFileCount(fileInput);
                }
            }
            
            reader.readAsDataURL(file);
        });
    });

    // Add event listener for Edit button if we're in view_car.php
    const editButton = document.getElementById('editButton');
    if (editButton) {
        editButton.addEventListener('click', () => {
            const inputs = document.querySelectorAll('#viewVehicleForm input, #viewVehicleForm textarea, #viewVehicleForm select');
            inputs.forEach(input => input.disabled = false);
            editButton.classList.add('d-none');
            document.getElementById('saveButton').classList.remove('d-none');
            
            // Enable the file input
            const fileInput = document.getElementById('image_upload');
            if (fileInput) {
                fileInput.disabled = false;
            }
        });
    }
}

function updateFileCount(fileInput) {
    const fileCount = document.querySelector('.file-count');
    if (fileCount) {
        const existingImageCount = document.querySelectorAll('.preview-item').length;
        fileCount.textContent = `${existingImageCount} Files Selected`;
    }
}

// For redirecting to Add vehicle content or approved content of back button
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
