<?php
require('config.php');

// Check for sessison start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = [];

// Get values from DOM inputs
$enteredUserID = isset($_POST['userId']) ? $_POST['userId'] : '';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '0';
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '0';
$selectedDate = isset($_POST['date']) ? $_POST['date'] : '0';

// Construct SQL query to display records on the given criteria
$sql = "SELECT * FROM order_table WHERE 1=1";
$params = [];
$paramTypes = '';

if (!empty($enteredUserID)) {
    $sql .= " AND User_ID = ?";
    $params[] = $enteredUserID;
    $paramTypes .= 's';
}

if ($selectedYear != '0') {
    $sql .= " AND YEAR(Date) = ?";
    $params[] = $selectedYear;
    $paramTypes .= 'i';
}

if ($selectedMonth != '0') {
    $sql .= " AND MONTH(Date) = ?";
    $params[] = $selectedMonth;
    $paramTypes .= 'i';
}

if ($selectedDate != '0') {
    $sql .= " AND DAY(Date) = ?";
    $params[] = $selectedDate;
    $paramTypes .= 'i';
}

// Prepare SQL statement
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

// Execute query & extract records
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    $response['message'] = $stmt->error;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
