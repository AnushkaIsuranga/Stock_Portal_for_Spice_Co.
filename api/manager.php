<?php
require 'config.php';

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the manager role
if (!isset($_SESSION["login"]) || $_SESSION["role"] !== 'manager') {
    header("Location: login.php");
    exit();
}

// Fetch new orders that need verification
$order_sql = "SELECT * FROM order_table WHERE needs_verification = TRUE";
$order_result = $conn->query($order_sql);

$sms; // SMS switch variable

// Handle order verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["verify_order"])) {
    $orderId = (int)$_POST["order_id"];
    $sql = "UPDATE order_table SET needs_verification = FALSE WHERE Order_ID = $orderId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Order verified successfully!');</script>";
        $sms = "order_verified";
        require 'sendSMS.php';
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
} elseif (isset($_POST["deny_order"])) {
    $orderId = (int)$_POST["order_id"];
    $delete_sql = "DELETE FROM order_table WHERE Order_ID = $orderId";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Order denied and deleted successfully!');</script>";
        echo "<script>window.location.href = 'manager.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
}

// Fetch current stock
$sql = "SELECT available_stock FROM stock_table WHERE product_id = 1";
$result = $conn->query($sql);
$stock_data = $result->fetch_assoc();

// Include the HTML file
include 'manager.html';
?>