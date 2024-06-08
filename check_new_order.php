<?php
require 'config.php';

// Query to get the count of new orders that need verification
$sql = "SELECT COUNT(*) as new_orders FROM order_table WHERE needs_verification = TRUE";
$result = $conn->query($sql);

$newOrders = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newOrders = $row['new_orders'];
}

echo json_encode(['new_orders' => $newOrders]);
?>
