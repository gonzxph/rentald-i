<?php
// Include the database connection
require_once 'config/db.php';

// Initialize variables
$available_cars = [];
$error_message = "";
$total_cars = 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 3; // Number of cars per page

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['page'])) {
    try {
        // Get user inputs
        $pax = $_POST['pax'] ?? $_GET['pax'] ?? null;
        $date = $_POST['dateTimeInput'] ?? $_GET['dateTimeInput'] ?? null;

        // Validate input
        if (!$pax || !$date) {
            throw new Exception("All fields are required.");
        }

        // Convert date range to start and end
        list($start, $end) = explode(" - ", $date);

        // Convert to MySQL DATETIME format
        $start_datetime = date("Y-m-d H:i:s", strtotime($start));
        $end_datetime = date("Y-m-d H:i:s", strtotime($end));

        // SQL Query to count total available cars
        $count_sql = "SELECT COUNT(*) FROM car
                      WHERE car_id NOT IN (
                          SELECT car_id
                          FROM rental
                          WHERE (RENT_PICKUP_DATETIME < :end_datetime AND RENT_DROPOFF_DATETIME > :start_datetime)
                      ) AND car_seats >= :pax";

        $count_stmt = $db->prepare($count_sql);
        $count_stmt->bindParam(':start_datetime', $start_datetime);
        $count_stmt->bindParam(':end_datetime', $end_datetime);
        $count_stmt->bindParam(':pax', $pax, PDO::PARAM_INT);
        $count_stmt->execute();
        $total_cars = $count_stmt->fetchColumn();

        // Calculate total pages and current page
        $total_pages = ceil($total_cars / $per_page);
        $page = max(1, min($page, $total_pages));
        $offset = ($page - 1) * $per_page;

        // SQL Query to find available cars with pagination
        $sql = "SELECT * FROM car
                WHERE car_id NOT IN (
                    SELECT car_id
                    FROM rental
                    WHERE (RENT_PICKUP_DATETIME < :end_datetime AND RENT_DROPOFF_DATETIME > :start_datetime)
                ) AND car_seats >= :pax
                LIMIT :offset, :per_page";

        // Prepare the query
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':start_datetime', $start_datetime);
        $stmt->bindParam(':end_datetime', $end_datetime);
        $stmt->bindParam(':pax', $pax, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch results
        $available_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log database errors
        error_log("Database Error: " . $e->getMessage());
        $error_message = "A database error occurred. Please try again later.";
    } catch (Exception $e) {
        // Log general errors
        error_log("Error: " . $e->getMessage());
        $error_message = $e->getMessage();
    }
}

// Include the view
/* require_once 'views/search_results.php'; */
?>