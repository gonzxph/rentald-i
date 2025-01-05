<?php
include 'db_conn.php';  // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penalty</title>
    <link rel="stylesheet" href="approved_content.css">
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
        <div class="header-container mb-4">
            <h1 class="text-left mb-3">Penalty</h1>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Search Bar Section -->
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Last Name" onkeyup="filterTable()">
            </div>
        </div>

        <!-- Table Section -->
        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="approvedTable">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Issue Type</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // SQL query to fetch data where rent_penalty_type is 'penalty'
                                $sql = "SELECT 
                                    users.user_fname, 
                                    users.user_lname, 
                                    rent_penalty.rent_penalty_type, 
                                    rent_penalty.rent_penalty_amount, 
                                    rent_penalty.rent_penalty_description, 
                                    rent_penalty.rent_penalty_date
                                FROM rent_penalty
                                JOIN rental ON rent_penalty.rental_id = rental.rental_id
                                JOIN users ON rental.user_id = users.user_id
                                WHERE rent_penalty.rent_penalty_type = 'Penalty' OR rent_penalty.rent_penalty_type = 'Additional Charge'";



                                // Execute the query and check for errors
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    $counter = 1; // For numbering the rows
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $counter++ . "</th>";
                                        echo "<td>" . htmlspecialchars($row['user_fname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['user_lname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_penalty_type']) . "</td>";
                                        echo "<td>" . htmlspecialchars(number_format($row['rent_penalty_amount'], 2)) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_penalty_description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rent_penalty_date']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No penalties found</td></tr>";
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
// Function to filter the table dynamically as the user types
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('approvedTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {  // Skip the header row
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 0) { 
            // Skip rows that do not contain data cells (like header or empty message rows)
            continue;
        }

        const lastName = cells[1] ? cells[1].textContent.toLowerCase() : '';

        // Check if the search term matches last name only
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
