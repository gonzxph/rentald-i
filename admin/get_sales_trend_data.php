<?php
// Include the database connection
include 'db_conn.php'; // Adjust the path if necessary

$sql = "
    SELECT 
        MONTH(rent_pickup_datetime) AS month,
        COUNT(*) / (YEAR(MAX(rent_pickup_datetime)) - YEAR(MIN(rent_pickup_datetime)) + 1) AS normalized_bookings_per_month,
        SUM(TIMESTAMPDIFF(HOUR, rent_pickup_datetime, rent_dropoff_datetime)) / 
            (YEAR(MAX(rent_pickup_datetime)) - YEAR(MIN(rent_pickup_datetime)) + 1) AS normalized_hours_per_month
    FROM rental
    WHERE YEAR(rent_pickup_datetime) < YEAR(CURDATE())
    GROUP BY MONTH(rent_pickup_datetime)
    ORDER BY month
";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "month" => $row["month"],
            "normalized_bookings_per_month" => $row["normalized_bookings_per_month"],
            "normalized_hours_per_month" => $row["normalized_hours_per_month"]
        ];
    }
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
