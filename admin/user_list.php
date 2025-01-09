<?php

session_start();
include 'db_conn.php';

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get total records
$total_query = "SELECT COUNT(*) as total FROM user";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Modified query to include pagination
$query = "SELECT * FROM user ORDER BY user_id DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
    ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getuser')) {
    
    $response = array();
    
    // Handle GET request for user data
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getuser') {
        if (!isset($_GET['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'User ID is required'
            ];
        } else {
            try {
                $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
                $query = "SELECT * FROM user WHERE user_id = '$user_id'";
                $result = mysqli_query($conn, $query);
                
                if ($user = mysqli_fetch_assoc($result)) {
                    $response = [
                        'success' => true,
                        'user' => $user
                    ];
                } else {
                    throw new Exception('User not found');
                }
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['action'])) {
            $response = [
                'success' => false,
                'message' => 'Action is required'
            ];
        } else {
            try {
                switch ($_POST['action']) {
                    case 'delete':
                        if (!isset($_POST['user_id'])) {
                            throw new Exception('User ID is required');
                        }
                        
                        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
                        $query = "DELETE FROM user WHERE user_id = '$user_id'";
                        
                        if (mysqli_query($conn, $query)) {
                            $response = [
                                'success' => true,
                                'message' => 'User deleted successfully'
                            ];
                        } else {
                            throw new Exception('Database error: ' . mysqli_error($conn));
                        }
                        break;

                    case 'adduser':
                        if (empty($_POST['firstName']) || empty($_POST['lastName']) || 
                            empty($_POST['email']) || empty($_POST['password']) || 
                            empty($_POST['role'])) {
                            throw new Exception('All fields are required');
                        }

                        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
                        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
                        $email = mysqli_real_escape_string($conn, $_POST['email']);
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $role = mysqli_real_escape_string($conn, $_POST['role']);

                        // Check if email exists
                        $check_query = "SELECT user_id FROM user WHERE user_email = '$email'";
                        $check_result = mysqli_query($conn, $check_query);
                        
                        if (mysqli_num_rows($check_result) > 0) {
                            throw new Exception('Email already exists');
                        }

                        $query = "INSERT INTO user (user_fname, user_lname, user_email, user_password, user_role) 
                                 VALUES ('$firstName', '$lastName', '$email', '$password', '$role')";
                        
                        if (mysqli_query($conn, $query)) {
                            $response = [
                                'success' => true,
                                'message' => 'User added successfully'
                            ];
                        } else {
                            throw new Exception('Database error: ' . mysqli_error($conn));
                        }
                        break;

                    case 'edituser':
                        if (empty($_POST['user_id']) || empty($_POST['firstName']) || 
                            empty($_POST['lastName']) || empty($_POST['email']) || 
                            empty($_POST['role']) || empty($_POST['status'])) {
                            throw new Exception('All fields are required');
                        }

                        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
                        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
                        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
                        $email = mysqli_real_escape_string($conn, $_POST['email']);
                        $role = mysqli_real_escape_string($conn, $_POST['role']);
                        $status = mysqli_real_escape_string($conn, $_POST['status']);

                        // Check if email exists for other users
                        $check_query = "SELECT user_id FROM user WHERE user_email = '$email' AND user_id != '$user_id'";
                        $check_result = mysqli_query($conn, $check_query);
                        
                        if (mysqli_num_rows($check_result) > 0) {
                            throw new Exception('Email already exists');
                        }

                        $query = "UPDATE user SET 
                                 user_fname = '$firstName',
                                 user_lname = '$lastName',
                                 user_email = '$email',
                                 user_role = '$role',
                                 user_status = '$status'
                                 WHERE user_id = '$user_id'";
                        
                        if (mysqli_query($conn, $query)) {
                            $response = [
                                'success' => true,
                                'message' => 'User updated successfully'
                            ];
                        } else {
                            throw new Exception('Database error: ' . mysqli_error($conn));
                        }
                        break;

                    default:
                        throw new Exception('Invalid action');
                }
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_content.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <div class="continer">
        
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Add New User
            </button>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                <th scope="col">No.</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td scope="row"><?php echo $counter++; ?></td>
                        <td><?php echo $row['user_fname']; ?></td>
                        <td><?php echo $row['user_lname']; ?></td>
                        <td><?php echo $row['user_email']; ?></td>
                        <td><?php echo $row['user_role']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" onclick="editUser(<?php echo $row['user_id']; ?>)">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $row['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Previous button -->
                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?php echo $page-1; ?>" <?php if($page <= 1){ echo 'tabindex="-1" aria-disabled="true"'; } ?>>Previous</a>
                </li>

                <?php
                $start_page = max(1, min($page - 4, $total_pages - 9));
                $end_page = min($total_pages, $start_page + 9);

                // Show first page and ellipsis if necessary
                if ($start_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Show page numbers
                for ($i = $start_page; $i <= $end_page; $i++) {
                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">
                            <a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>
                          </li>';
                }

                // Show last page and ellipsis if necessary
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                ?>

                <!-- Next button -->
                <li class="page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?php echo $page+1; ?>" <?php if($page >= $total_pages){ echo 'tabindex="-1" aria-disabled="true"'; } ?>>Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Add this edit modal HTML after your add user modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="mb-3">
                            <label for="edit_firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-control" id="edit_role" name="role" required>
                                <option value="USER">User</option>
                                <option value="ADMIN">Admin</option>
                                <option value="DRIVER">Driver</option>
                                <option value="AGENT">Agent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="ACTIVE">Active</option>
                                <option value="DEACTIVATED">Deactivate</option>
                            </select>
                        </div>
                        <div id="editFormMessage" class="alert" style="display: none;"></div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="USER">User</option>
                                <option value="ADMIN">Admin</option>
                                <option value="DRIVER">Driver</option>
                                <option value="AGENT">Agent</option>
                            </select>
                        </div>
                        <div id="formMessage" class="alert" style="display: none;"></div>
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function changePage(pageNumber) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `user_list.php?page=${pageNumber}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('main-content').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    </script>
</body>
</html>