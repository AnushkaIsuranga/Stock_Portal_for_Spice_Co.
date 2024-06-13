<?php
require('config.php');

// Check if a session is already active before starting a new one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['id'];

// Capture filter data from AJAX request
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '0';
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '0';
$selectedDate = isset($_POST['date']) ? $_POST['date'] : '0';

// Base query
$sql = "SELECT Order_ID, Date, Quantity, Price_Rs FROM order_table WHERE user_id = ?";
$whereClause = "";
$params = [$id];
$paramTypes = "i";

// Add filters if they are set
if ($selectedYear != '0') {
    $whereClause .= " AND YEAR(Date) = ?";
    $params[] = $selectedYear;
    $paramTypes .= "i";
}

if ($selectedMonth != '0') {
    $whereClause .= " AND MONTH(Date) = ?";
    $params[] = $selectedMonth;
    $paramTypes .= "i";
}

if ($selectedDate != '0') {
    $whereClause .= " AND DAY(Date) = ?";
    $params[] = $selectedDate;
    $paramTypes .= "i";
}

// Append WHERE clause
$sql .= $whereClause;

$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();

// Encode data into JSON and return it
header('Content-Type: application/json');
echo json_encode($data);
?>
