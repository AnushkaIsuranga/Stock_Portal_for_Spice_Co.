<?php
require('config.php');

// Check for sessison start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = [];

// Get values from DOM inputs
$enteredUserID = isset($_POST['userId']) ? $_POST['userId'] : '';

// Construct SQL query to display records on the given criteria
$sql = "SELECT * FROM users_table WHERE 1=1";
$params = [];
$paramTypes = '';

if (!empty($enteredUserID)) {
    $sql .= " AND User_ID = ?";
    $params[] = $enteredUserID;
    $paramTypes .= 's';
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
