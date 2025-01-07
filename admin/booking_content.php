<?php
include 'db_conn.php';  // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Review</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="booking_content.css">
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
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .view-icon {
            color: blue;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .add-penalty-icon {
            color: red;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
        <div class="header-container mb-4">
            <h1 class="text-left mb-3">Booking Review</h1>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Last Name" onkeyup="filterTable()">
            </div>
        </div>

        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table table-bordered" id="approvedTable">
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
                                WHERE p.pay_type = 'Reservation' AND r.rent_status = 'Pending'";

                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    $counter = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $counter++ . "</th>";
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
                              
                                    }
                                } else {
                                    echo "<tr><td colspan='9' style='text-align:center;'>No approved rentals found</td></tr>";
                                }

                                $conn->close();
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
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('approvedTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 0) {
            continue;
        }

        const lastName = cells[1]?.textContent.toLowerCase() || "";

        if (lastName.includes(filter)) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>
</body>
</html>









