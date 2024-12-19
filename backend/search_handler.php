<?php
// Include the database connection
require_once __DIR__ . '/../config/db.php';

// Initialize variables
$available_cars = [];
$error_message = "";
$total_cars = 0;
$total_pages = 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 3; // Number of cars per page

// Retrieve filter values
$date = $_POST['dateTimeInput'] ?? $_GET['dateTimeInput'] ?? '';
$vehicle_types = $_POST['vehicle'] ?? $_GET['vehicle'] ?? [];
$transmission_types = $_POST['transmission'] ?? $_GET['transmission'] ?? [];
$durationDay = $_POST['durationDay'] ?? $_GET['durationDay'] ?? '';
$durationHour = $_POST['durationHour'] ?? $_GET['durationHour'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['page'])) {
    try {
        // Validate input
        if (empty($date)) {
            throw new Exception("Date range is required.");
        }

        // Convert date range to start and end
        list($start, $end) = explode(" - ", $date);
        if (!$start || !$end) {
            throw new Exception("Invalid date range format.");
        }

        // Convert to MySQL DATETIME format
        $start_datetime = date("Y-m-d H:i:s", strtotime($start));
        $end_datetime = date("Y-m-d H:i:s", strtotime($end));

        // Prepare the WHERE clause for filters
        $where_clause = "WHERE car.car_id NOT IN (
            SELECT rental.car_id
            FROM rental
            WHERE NOT (
                RENT_PICKUP_DATETIME >= :end_datetime OR RENT_DROPOFF_DATETIME <= :start_datetime
            )
        )";

        $params = [
            ':start_datetime' => $start_datetime,
            ':end_datetime' => $end_datetime
        ];

        if (!empty($vehicle_types)) {
            $vehicle_placeholders = [];
            foreach ($vehicle_types as $index => $type) {
                $key = ":vehicle_type_$index";
                $vehicle_placeholders[] = $key;
                $params[$key] = $type;
            }
            $where_clause .= " AND car_type IN (" . implode(', ', $vehicle_placeholders) . ")";
        }

        if (!empty($transmission_types)) {
            $transmission_placeholders = [];
            foreach ($transmission_types as $index => $type) {
                $key = ":transmission_type_$index";
                $transmission_placeholders[] = $key;
                $params[$key] = $type;
            }
            $where_clause .= " AND car_transmission_type IN (" . implode(', ', $transmission_placeholders) . ")";
        }

        // Pagination offset calculation
        $offset = ($page - 1) * $per_page;

        // SQL Query to count total available cars
        $count_sql = "SELECT COUNT(*) FROM car $where_clause";

        $count_stmt = $db->prepare($count_sql);
        $count_stmt->execute($params);
        $total_cars = $count_stmt->fetchColumn();

        // Calculate total pages and current page
        $total_pages = ceil($total_cars / $per_page);
        $page = max(1, min($page, $total_pages));
        $offset = ($page - 1) * $per_page;

        // SQL Query to find available cars with pagination
        $sql = "
    SELECT 
        car.*, 
        COALESCE(MIN(car_image.img_url), 'default.png') AS img_url 
    FROM 
        car 
    LEFT JOIN 
        car_image 
    ON 
        car.car_id = car_image.car_id 
    $where_clause 
    GROUP BY 
        car.car_id 
    LIMIT :offset, :per_page
";
        

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch results
        $available_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log database errors
        error_log("Database Error in search.php: " . $e->getMessage());
        $error_message = "Database Error: " . $e->getMessage(); // Temporarily show the actual error
    } catch (Exception $e) {
        // Log general errors
        error_log("Error in search.php: " . $e->getMessage());
        $error_message = $e->getMessage();
    }
}
?>