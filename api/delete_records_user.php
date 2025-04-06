<?php
require('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Setting up variable with inputs passed through the AJAX
    $userId = isset($_POST['userId']) ? $_POST['userId'] : '';

    // Construct SQL query to delete records based on the given criteria
    $sql = "DELETE FROM users_table WHERE 1=1";

    if (!empty($userId)) {
        $sql .= " AND User_ID = ?";
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
