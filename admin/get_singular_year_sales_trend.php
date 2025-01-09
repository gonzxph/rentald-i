<?php
// Include the database connection
include 'db_conn.php';

// Get the selected year
$year = $_GET['year']; // The selected year passed from the client

// SQL Query to fetch required data for the given year
$sql = "
    SELECT 
        MONTH(rent_pickup_datetime) AS month,
        COUNT(*) AS total_bookings,
        SUM(TIMESTAMPDIFF(HOUR, rent_pickup_datetime, rent_dropoff_datetime)) AS total_hours,
        AVG(TIMESTAMPDIFF(HOUR, rent_pickup_datetime, rent_dropoff_datetime)) AS avg_hours_per_booking
    FROM rental
    WHERE YEAR(rent_pickup_datetime) = ?
    GROUP BY MONTH(rent_pickup_datetime)
    ORDER BY month
";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'month' => $row['month'],
        'total_bookings' => $row['total_bookings'],
        'total_hours' => $row['total_hours'],
        
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
