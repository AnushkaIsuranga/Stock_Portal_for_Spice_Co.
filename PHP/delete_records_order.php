<?php
require('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = isset($_POST['userId']) ? $_POST['userId'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    $month = isset($_POST['month']) ? $_POST['month'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';

    // Construct SQL query to delete records based on the given criteria
    $sql = "DELETE FROM order_table WHERE 1=1";

    if (!empty($userId)) {
        $sql .= " AND User_ID = ?";
    }

    if ($year != '0') {
        $sql .= " AND YEAR(Date) = ?";
    }

    if ($month != '0') {
        $sql .= " AND MONTH(Date) = ?";
    }

    if ($date != '0') {
        $sql .= " AND DAY(Date) = ?";
    }

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $paramTypes = '';
        $params = [];

        if (!empty($userId)) {
            $paramTypes .= 'i';
            $params[] = $userId;
        }

        if ($year != '0') {
            $paramTypes .= 'i';
            $params[] = $year;
        }

        if ($month != '0') {
            $paramTypes .= 'i';
            $params[] = $month;
        }

        if ($date != '0') {
            $paramTypes .= 'i';
            $params[] = $date;
        }

        if (!empty($params)) {
            $stmt->bind_param($paramTypes, ...$params);
        }
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Records deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Error deleting records: ' . $stmt->error];
        }

        $stmt->close();
    } else {
        $response = ['success' => false, 'message' => 'Error preparing statement: ' . $conn->error];
    }

    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
}
?>
