<?php
// Include the database connection
include 'db_conn.php'; // Adjust the path if necessary

// SQL Query to fetch required data
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

// Execute query
$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'month' => $row['month'],
            'normalized_bookings_per_month' => $row['normalized_bookings_per_month'],
            'normalized_hours_per_month' => $row['normalized_hours_per_month'],
        ];
    }
}
$conn->close();

// Error response if no data
if (empty($data)) {
    echo json_encode(['error' => 'No data found']);
    exit;
}

// Normalize data function
function normalize($values) {
    $min = min($values);
    $max = max($values);
    return array_map(fn($v) => ($max - $min) ? ($v - $min) / ($max - $min) : 0, $values);
}

// Normalize bookings and hours
$bookings = array_column($data, 'normalized_bookings_per_month');
$hours = array_column($data, 'normalized_hours_per_month');

$normalizedBookings = normalize($bookings);
$normalizedHours = normalize($hours);

// Combine normalized data into an array
$clusteringData = array_map(null, $normalizedBookings, $normalizedHours);

// Euclidean distance calculation
function euclidean_distance($point1, $point2) {
    return sqrt(pow($point2[0] - $point1[0], 2) + pow($point2[1] - $point1[1], 2));
}

// Assign clusters
function assign_clusters($data, $centroids) {
    $clusters = array_fill(0, count($centroids), []);
    foreach ($data as $point) {
        $distances = array_map(fn($centroid) => euclidean_distance($point, $centroid), $centroids);
        $clusterIndex = array_search(min($distances), $distances);
        $clusters[$clusterIndex][] = $point;
    }
    return $clusters;
}

// Update centroids
function update_centroids($clusters) {
    return array_map(function($cluster) {
        $n = count($cluster);
        if ($n > 0) {
            $xSum = array_sum(array_column($cluster, 0));
            $ySum = array_sum(array_column($cluster, 1));
            return [$xSum / $n, $ySum / $n];
        }
        return [0, 0];
    }, $clusters);
}

// K-means algorithm
function kmeans($data, $k, $maxIterations, $epsilon) {
    $centroids = array_map(function($i) use ($data) {
        return $data[array_rand($data)];
    }, range(0, $k - 1));

    for ($i = 0; $i < $maxIterations; $i++) {
        $clusters = assign_clusters($data, $centroids);
        $newCentroids = update_centroids($clusters);
        
        $converged = true;
        foreach ($centroids as $index => $centroid) {
            if (euclidean_distance($centroid, $newCentroids[$index]) > $epsilon) {
                $converged = false;
                break;
            }
        }
        if ($converged) break;
        $centroids = $newCentroids;
    }
    return ['clusters' => $clusters, 'centroids' => $centroids];
}

// Apply KMeans clustering
$k = 3;
$maxIterations = 100;
$epsilon = 0.01;

$result = kmeans($clusteringData, $k, $maxIterations, $epsilon);

// Classify clusters based on centroids
$centroidMagnitudes = array_map(fn($c) => sqrt(pow($c[0], 2) + pow($c[1], 2)), $result['centroids']);
arsort($centroidMagnitudes);

$clusterLabels = [];
$sortedKeys = array_keys($centroidMagnitudes);
$clusterLabels[$sortedKeys[0]] = 'Peak';
$clusterLabels[$sortedKeys[1]] = 'Mid';
$clusterLabels[$sortedKeys[2]] = 'Low';

// Assign months to clusters
$monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$clusterResult = [];

foreach ($result['clusters'] as $clusterIndex => $cluster) {
    foreach ($cluster as $point) {
        $monthIndex = array_search($point, $clusteringData);
        if ($monthIndex !== false) {
            $clusterResult[$monthNames[$monthIndex]] = $clusterLabels[$clusterIndex];
        }
    }
}

// Return results
header('Content-Type: application/json');
echo json_encode([
    'clusters' => $clusterResult,
    'centroids' => $result['centroids']
]);
?>
