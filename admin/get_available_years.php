<?php
// Include the database connection
include 'db_conn.php'; // Adjust the path if necessary

// SQL Query to fetch available years
$yearsSql = "
SELECT 
    YEAR(rent_pickup_datetime) AS year
FROM rental
WHERE YEAR(rent_pickup_datetime) < YEAR(CURDATE())
GROUP BY YEAR(rent_pickup_datetime)
";

// Execute the query for years
$yearsResult = $conn->query($yearsSql);
$availableYears = [];

if ($yearsResult->num_rows > 0) {
    while ($row = $yearsResult->fetch_assoc()) {
        $availableYears[] = $row['year'];
    }
}

// Return the available years
if (empty($availableYears)) {
    echo json_encode(['success' => false, 'message' => 'No years available']);
} else {
    echo json_encode(['success' => true, 'years' => $availableYears]);
}
?>