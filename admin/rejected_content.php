<?php
include 'db_conn.php';  // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Status</title>
    <link rel="stylesheet" href="approved_content.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
        <!-- Header Section -->
        <div class="header-container mb-4">
            <h1 class="text-left mb-3">Rejected</h1>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Approved Button -->
            <div class="btn-container">
                <button id="viewApprovedButton" class="btn btn-outline-danger" onclick="loadContent('approved_content.php')">View Approved List</button>
            </div>

            <!-- Search Bar Section -->
            <div class="search-container">
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                // SQL query to fetch data where rental_status is 'REJECTED'
                                $sql = "SELECT users.user_fname, users.user_lname, rental.rental_pickup_datetime, rental.rental_dropoff_datetime, 
                                        rental.rental_pickup_location, rental.rental_dropoff_location
                                        FROM rental
                                        JOIN users ON rental.user_id = users.user_id
                                        WHERE rental.rental_status = 'REJECTED'";

                                // Execute the query and check for errors
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    $counter = 1; // For numbering the rows
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $counter++ . "</th>";
                                        echo "<td>" . htmlspecialchars($row['user_fname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['user_lname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rental_pickup_datetime']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rental_dropoff_datetime']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rental_pickup_location']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['rental_dropoff_location']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No data found</td></tr>";
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
</body>
</html>
