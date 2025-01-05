<?php
include "db_conn.php";

// Get search query and availability filter from POST request
$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';
$availabilityFilter = isset($_POST['availabilityFilter']) ? $_POST['availabilityFilter'] : 'ShowAll';

// Prepare the SQL query based on search and availability filter
if (!empty($searchQuery)) {
    $sql = "SELECT * FROM car WHERE (car_model LIKE ? OR car_brand LIKE ?)";
    if ($availabilityFilter != 'ShowAll') {
        $sql .= " AND car_availability = ?";
    }
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $searchQuery . "%"; 
    if ($availabilityFilter != 'ShowAll') {
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $availabilityFilter);
    } else {
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
    }
} else {
    $sql = "SELECT * FROM car";
    if ($availabilityFilter != 'ShowAll') {
        $sql .= " WHERE car_availability = ?";
    }
    $stmt = $conn->prepare($sql);
    if ($availabilityFilter != 'ShowAll') {
        $stmt->bind_param('s', $availabilityFilter);
    }
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Invalid Query!");
}

// Display the results dynamically
while ($row = $result->fetch_assoc()) {
    echo '
    <tr>
        <th>' . $row['car_id'] . '</th>
        <td class="car-model">' . $row['car_model'] . '</td>
        <td class="car-brand">' . $row['car_brand'] . '</td>
        <td>' . $row['car_availability'] . '</td>
        <td>
            <a href="view_car.php?car_id=' . $row['car_id'] . '" class="btn btn-primary"><i class="far fa-eye"></i></a>
            <a href="delete_car.php?car_id=' . $row['car_id'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this vehicle?\');"><i class="far fa-trash-alt"></i></a>
        </td>
    </tr>';
}
?>
