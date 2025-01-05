<?php
// Include database connection
include 'db_conn.php';

// Get the search query from the URL, if any
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch data for payments where payment_type is 'Reservation' and rent_status is 'PENDING'
$sql = "SELECT 
            u.user_fname, 
            u.user_lname, 
            p.pay_status, 
            c.car_brand, 
            c.car_model,
            r.rental_id,
            r.rental_pax
        FROM payment p
        JOIN rental r ON p.rental_id = r.rental_id
        JOIN user u ON r.user_id = u.user_id
        JOIN car c ON r.car_id = c.car_id
        WHERE r.rent_status ='PENDING'";

// Modify the query if there's a search term
if (!empty($search)) {
    $sql .= " AND (u.user_fname LIKE '%$search%' OR u.user_lname LIKE '%$search%')";
}

$result = $conn->query($sql);
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
        <!-- Header Section -->
        <div class="header-container">
            <div class="header-left">
                <h1>Booking Review</h1>
            </div>
            <div class="header-right">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Last Name" value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>

        <!-- Table Section -->
        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table table-bordered" id="bookingTable">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Payment Status</th>
                                    <th scope="col">Selected Car</th>
                                    <th scope="col">Pax</th>
                                    <th scope="col">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    $counter = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>{$counter}</th>";
                                        echo "<td>{$row['user_fname']}</td>";
                                        echo "<td>{$row['user_lname']}</td>";
                                        echo "<td>{$row['pay_status']}</td>";
                                        echo "<td>{$row['car_brand']} {$row['car_model']}</td>";
                                        echo "<td>{$row['rental_pax']}</td>";
                                        echo "<td>
                                                    <a href='view_booking_details.php?rental_id={$row['rental_id']}' class='btn btn-primary'>
                                                        <i class='far fa-eye'></i>
                                                    </a>
                                                </td>";
                                        echo "</tr>";
                                        $counter++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No data available</td></tr>";
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
// Function to filter the table dynamically as the user types
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("bookingTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) { // Skip the header row
        const cells = rows[i].getElementsByTagName("td");
        const lastName = cells[2]?.textContent.toLowerCase() || ""; // Only check last name column

        // Match the filter with the last name
        if (lastName.includes(filter)) {
            rows[i].style.display = ""; // Show row
        } else {
            rows[i].style.display = "none"; // Hide row
        }
    }
}

// Attach filterTable function to the search input's input event
document.getElementById("searchInput").addEventListener("input", filterTable);
</script>

</body>
</html>
