<?php
// Include database connection
include 'db_conn.php';

// Get the search query from the URL, if any
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch data for payments where payment_type is 'Cash' and rental_status is 'PENDING'
$sql = "SELECT 
            u.user_fname, 
            u.user_lname, 
            p.payment_status, 
            c.car_brand, 
            c.car_model,
            r.rental_id
        FROM payment p
        JOIN rental r ON p.rental_id = r.rental_id
        JOIN users u ON r.user_id = u.user_id
        JOIN car c ON r.car_id = c.car_id
        WHERE p.payment_type = 'Cash' 
        AND r.rental_status = 'PENDING'";

// Modify the query to include search functionality
if ($search) {
    $sql .= " AND (u.user_fname LIKE ? OR u.user_lname LIKE ?)";
}

$stmt = $conn->prepare($sql);

// Bind the parameters for the search
if ($search) {
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Review</title>
    <link rel="stylesheet" href="booking_content.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
    <style>
        .table-container {
            max-height: 300px; 
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
        <div class="header-container">
            <div class="header-left">
                <h1>Booking Review</h1>
            </div>
            <div class="header-right">
                <form method="GET" action="booking_content.php">
                    <input type="text" class="form-control" name="search" placeholder="Search by First or Last Name" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    <button type="submit" class="btn btn-outline-success">Search</button>
                </form>
            </div>

        </div>
        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Payment Status</th>
                                    <th scope="col">Selected Car</th>
                                    <th scope="col">View</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    $counter = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>{$counter}</th>";
                                        echo "<td>{$row['user_fname']}</td>";
                                        echo "<td>{$row['user_lname']}</td>";
                                        echo "<td>{$row['payment_status']}</td>";
                                        echo "<td>{$row['car_brand']} {$row['car_model']}</td>";
                                        echo "<td>
                                                <div class='d-flex flex-column flex-sm-row gap-1'>
                                                    <a href='view_booking_details.php?rental_id={$row['rental_id']}' class='btn btn-primary'>
                                                        <i class='far fa-eye'></i>
                                                    </a>
                                                </div>
                                            </td>";
                                        echo "</tr>";
                                        $counter++;
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script> 

function goToBookingContent() {
    var searchQuery = document.querySelector('input[name="search"]').value; // Get the search input value
    var url = 'index.php?content=booking_content.php'; // Default URL

    if (searchQuery) {
        // If there is a search query, append it to the URL
        url += '&search=' + encodeURIComponent(searchQuery);
    }

    window.location.href = url; // Redirect to the URL
}

</script>
</body>
</html>
