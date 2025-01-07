<?php
include 'db_conn.php';  // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Status</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="rejected_content.css">
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
            <h1 class="text-left mb-3">Rejected Rentals</h1>
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
                                    <th scope="col">Pick-up Date</th>
                                    <th scope="col">Drop-off Date</th>
                                    <th scope="col">Pick-up Location</th>
                                    <th scope="col">Drop-off Location</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                           $sql = "SELECT 
                                r.rental_id, 
                                u.user_fname, 
                                u.user_lname, 
                                r.rent_pickup_datetime, 
                                r.rent_dropoff_datetime, 
                                r.rent_pickup_location, 
                                r.rent_dropoff_location
                            FROM payment p
                            JOIN rental r ON p.rental_id = r.rental_id
                            JOIN user u ON r.user_id = u.user_id
                            WHERE r.rent_status = 'REJECTED'
                            ORDER BY r.rent_rejected_datetime DESC";
               

                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    $counter = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $counter++ . "</th>";
                                        echo "<td>" . htmlspecialchars($row['user_fname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['user_lname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_pickup_datetime']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_dropoff_datetime']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_pickup_location']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_dropoff_location']) . "</td>";
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
