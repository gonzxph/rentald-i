<?php
session_start();
include 'db_conn.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;

// Get total records with search
$total_query = "SELECT COUNT(*) as total FROM user 
                WHERE user_email LIKE '%$search%' 
                OR user_fname LIKE '%$search%' 
                OR user_lname LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get filtered users
$query = "SELECT * FROM user 
          WHERE user_email LIKE '%$search%' 
          OR user_fname LIKE '%$search%' 
          OR user_lname LIKE '%$search%' 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

$users = [];
while($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

$response = [
    'users' => $users,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ]
];

header('Content-Type: application/json');
echo json_encode($response); 