<?php
require 'config.php';

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the admin role
if (!isset($_SESSION["login"]) || $_SESSION["role"] != 'admin') {
    header("Location: login.php");
    exit();
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_stock"])) {
        $new_stock = (int)$_POST["new_stock"];
        if ($new_stock >= 0) {
            try {
                // Update stock
                $product_id = 1;
                $updateStockSql = "UPDATE stock_table SET available_stock = available_stock + ? WHERE product_id = ?";
                $stmt = $conn->prepare($updateStockSql);
                $stmt->bind_param("ii", $new_stock, $product_id);
                $stmt->execute();
                $successMsg = "Stock updated successfully.";
            } catch (Exception $e) {
                $errorMsg = "Error updating stock: " . $e->getMessage();
            }
        } else {
            $errorMsg = "Invalid stock quantity.";
        }
    }
}

// Fetch current stock
$sql = "SELECT available_stock FROM stock_table WHERE product_id = 1";
$result = $conn->query($sql);
$stock_data = $result->fetch_assoc();

// Include the HTML file
include 'admin.html';
?>
