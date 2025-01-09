<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role'])) {
    header('Location: ../signin.php');
    exit();
}

// Check if user is neither ADMIN nor AGENT
if($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'AGENT') {
    header('Location: ../signin.php');
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
                    <?php if($_SESSION['user_role'] === 'ADMIN'): ?>
                        <li onclick="loadContent('dashboard_content.php')" id="dashboard" class="sidebar-item" aria-label="Dashboard">
                            <img src="admin_dashboard_pics/dashboard.png" alt="Speedometer Icon" class="sidebar-icon">
                            Dashboard
                        </li>
                        <li onclick="loadContent('add_vehicle_content.php')" id="add-vehicle" class="sidebar-item" aria-label="Add Vehicle">
                            <img src="admin_dashboard_pics/add_vehicle.png" alt="Car Icon" class="sidebar-icon">
                            Add Vehicle
                        </li>
                        <li onclick="loadContent('user_list.php')" id="user-list" class="sidebar-item" aria-label="User List">
                            <img src="admin_dashboard_pics/users.png" alt="Car Icon" class="sidebar-icon">
                            User List
                        </li>
                    <?php endif; ?>
                    
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
                    
                    <?php if($_SESSION['user_role'] === 'ADMIN'): ?>
                        <li id="sales" class="sidebar-item" aria-label="Sales/Sales Trend">
                            <a href="sales_trend_content.php" class="sidebar-link">
                                <img src="admin_dashboard_pics/sales.png" alt="Bar Chart Icon" class="sidebar-icon">
                                Sales/Sales Trend
                            </a>
                        </li>
                    <?php endif; ?>
                    
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
            
            // Initialize all necessary listeners if we're on user list page
            if (page.includes('user_list.php')) {
                attachPaginationListeners();
                initializeUserForm();
                initializeEditUserForm();
                attachEditDeleteHandlers();
            }

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

// Add this new function to handle the user form initialization
function initializeUserForm() {
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        // Remove any existing event listeners
        addUserForm.replaceWith(addUserForm.cloneNode(true));
        
        // Get the new form reference after cloning
        const newForm = document.getElementById('addUserForm');
        
        newForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'adduser');
            
            fetch('user_list.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('formMessage');
                
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = 'User added successfully!';
                    
                    // Reset form
                    this.reset();
                    
                    // Properly close the modal and remove backdrop
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    modal.hide();
                    
                    // Remove modal backdrop and restore body scrolling
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    // Refresh the user list content
                    loadContent('user_list.php');
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = data.message || 'Error adding user';
                }
                messageDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('formMessage');
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'An error occurred';
                messageDiv.style.display = 'block';
            });
        });
    }
}

// Add this new function to handle edit user form submission
function initializeEditUserForm() {
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'edituser');
            
            fetch('user_list.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('editFormMessage');
                
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = 'User updated successfully!';
                    
                    // Get all modals and clean them up properly
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modalEl => {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    });

                    // Clean up modal artifacts
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                    
                    // Refresh the user list
                    loadContent('user_list.php');
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = data.message || 'Error updating user';
                }
                messageDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('editFormMessage');
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'An error occurred';
                messageDiv.style.display = 'block';
            });
        });

        // Add modal close handler
        const editModal = document.getElementById('editUserModal');
        if (editModal) {
            editModal.addEventListener('hidden.bs.modal', function () {
                // Clean up modal artifacts
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });

            // Handle close button click
            const closeButton = editModal.querySelector('.btn-close');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(editModal);
                    if (modal) {
                        modal.hide();
                        // Clean up modal artifacts
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');
                    }
                });
            }
        }
    }
}

function deleteUser(userId) {
    // Create modal element properly
    const modalElement = document.createElement('div');
    modalElement.className = 'modal fade';
    modalElement.id = 'deleteConfirmModal';
    modalElement.setAttribute('tabindex', '-1');
    modalElement.setAttribute('aria-labelledby', 'deleteConfirmModalLabel');
    modalElement.setAttribute('aria-hidden', 'true');
    
    modalElement.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modalElement);
    
    // Create the Bootstrap modal instance
    const confirmModal = new bootstrap.Modal(modalElement);
    
    // Handle the delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('user_id', userId);
        
        fetch('user_list.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            confirmModal.hide();
            // Remove modal and backdrop
            modalElement.remove();
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            if (data.success) {
                // Show success message using Bootstrap toast
                const toastContainer = document.createElement('div');
                toastContainer.className = 'position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1070';
                toastContainer.innerHTML = `
                    <div class="toast align-items-center text-white bg-success border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                User deleted successfully
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toastContainer);
                
                const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
                toast.show();
                
                // Remove toast after it's hidden
                toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
                    toastContainer.remove();
                });

                // Refresh the user list
                loadContent('user_list.php');
            } else {
                // Show error message
                const errorToast = document.createElement('div');
                errorToast.className = 'position-fixed top-0 end-0 p-3';
                errorToast.style.zIndex = '1070';
                errorToast.innerHTML = `
                    <div class="toast align-items-center text-white bg-danger border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${data.message || 'Error deleting user'}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(errorToast);
                
                const toast = new bootstrap.Toast(errorToast.querySelector('.toast'));
                toast.show();
                
                errorToast.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
                    errorToast.remove();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            confirmModal.hide();
            modalElement.remove();
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
            document.body.classList.remove('modal-open');
        });
    });

    confirmModal.show();
}

// Add this new function to handle pagination
function attachPaginationListeners() {
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageNum = this.getAttribute('data-page');
            if (pageNum && !this.parentElement.classList.contains('disabled')) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `user_list.php?page=${pageNum}`, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('main-content').innerHTML = xhr.responseText;
                        // Reattach all event listeners after loading new page
                        attachPaginationListeners();
                        initializeUserForm();
                        initializeEditUserForm();
                        
                        // Reattach edit and delete button handlers
                        attachEditDeleteHandlers();
                    }
                };
                xhr.send();
            }
        });
    });
}

// Add this new function to handle edit and delete button events
function attachEditDeleteHandlers() {
    // Attach edit button handlers
    document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#editUserModal"]').forEach(button => {
        button.onclick = function() {
            const userId = this.getAttribute('onclick').match(/\d+/)[0];
            editUser(userId);
        };
    });

    // Attach delete button handlers
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.onclick = function() {
            const userId = this.getAttribute('onclick').match(/\d+/)[0];
            deleteUser(userId);
        };
    });
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
    const userRole = '<?php echo $_SESSION["user_role"]; ?>';

    if (content) {
        // Load specified content if provided
        loadContent(content);
    } else {
        // Default to booking content for AGENT and dashboard for ADMIN
        if (userRole === 'AGENT') {
            loadContent('booking_content.php');
        } else {
            loadContent('dashboard_content.php');
        }
    }

    // Ensures the appropriate link is highlighted on initial load
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(item => {
        item.classList.remove('selected');
    });
    const defaultItem = userRole === 'AGENT' 
        ? document.querySelector('.sidebar-item[onclick*="booking_content.php"]')
        : document.querySelector('.sidebar-item[onclick*="dashboard_content.php"]');
    if (defaultItem) {
        defaultItem.classList.add('selected');
    }
});

// Add this function to handle getting user data for editing
function editUser(userId) {
    fetch(`user_list.php?action=getuser&user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the edit form with user data
                document.getElementById('edit_user_id').value = data.user.user_id;
                document.getElementById('edit_firstName').value = data.user.user_fname;
                document.getElementById('edit_lastName').value = data.user.user_lname;
                document.getElementById('edit_email').value = data.user.user_email;
                document.getElementById('edit_role').value = data.user.user_role;
                document.getElementById('edit_status').value = data.user.user_status;
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            } else {
                console.error('Error fetching user data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
