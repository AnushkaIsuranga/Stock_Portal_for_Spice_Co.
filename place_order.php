<?php
require 'config.php'; // Assuming config.php has your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    // Check if a session is already active (optional, might be redundant)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Access user ID from the session
    $id = $_SESSION["id"];

    // Fetch current stock
    $sql = "SELECT available_stock FROM stock_table WHERE product_id = 1";
    $result = $conn->query($sql);
    $stock_data = $result->fetch_assoc();

    // Setting user inputs to variables
    $organization = htmlspecialchars($_POST["organization"]);
    $quantity = (double)$_POST["quantity"];
    $address1 = htmlspecialchars($_POST["address1"]);
    $address2 = htmlspecialchars($_POST["address2"]);
    $recite = $_FILES["recite"];
    $price = $quantity * 2700;

    $errorMsg = "";

    $sms; // SMS switch variable

    // Input validation
    if (empty($organization)) {
        $errorMsg = "Please enter the organization name.";
    } else if ($quantity <= 0) {
        $errorMsg = "Please enter a valid quantity (positive number).";
    } else if ($quantity > $stock_data['available_stock']) {
        $errorMsg = "Sorry. Our current stock level is $stock_data[available_stock] kg";
    } else if (empty($address1)) {
        $errorMsg = "Please enter the address line 1.";
    } else if (empty($address2)) {
        $errorMsg = "Please enter the address line 2.";
    } else if (empty($recite['name'])) {
        $errorMsg = "Please select a picture of your payment receipt.";
    } else {
        // Validate picture format
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $pictureExtension = strtolower(pathinfo($recite['name'], PATHINFO_EXTENSION));
        if (!in_array($pictureExtension, $allowedExtensions)) {
            $errorMsg = "Invalid picture format. Only JPEG and PNG files are allowed.";
        } else {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetFile = $targetDir . basename(uniqid() . "." . $pictureExtension);

            if (move_uploaded_file($recite['tmp_name'], $targetFile)) {

                try {
                    $decremented_stock = $stock_data['available_stock'] - $quantity;
                    
                    // Check if stock update was successful
                    if ($decremented_stock > 0) {
                        // Executing SQL query to insert order
                        $sql = "INSERT INTO order_table (User_ID, Date, Quantity, Price_Rs, Recite, Add_Line1, Add_Line2, needs_verification) VALUES ($id, CURDATE(), $quantity, $price, '$targetFile', '$organization, $address1', '$address2', TRUE)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();

                        // Executing SQL query to insert order
                        $product_id = 1;
                        $updateStockSql = "UPDATE stock_table SET available_stock = available_stock - $quantity WHERE product_id = $product_id";
                        $stock_stmt = $conn->prepare($updateStockSql);
                        $stock_stmt->execute();

                        if ($stmt->affected_rows > 0) {
                            $conn->commit();
                            echo "<script>alert('Order placed successfully! Your order ID is: " . $stmt->insert_id . "');window.location.href = 'index.php';</script>";
                            $sms = "order_placed";
                            require 'sendSMS.php';
                            exit(); 
                        } else {
                            $errorMsg = "Error: " . $stmt->error;
                            $conn->rollback();
                        }
                    } else {
                        $errorMsg = "Error updating stock: Not enough stock available.";
                        $conn->rollback();
                    }
                } catch (Exception $e) {
                    $errorMsg = "Database error: " . $e->getMessage();
                }
            } else {
                $errorMsg = "Error uploading picture. Please try again.";
            }
        }
    }
}

// Include the HTML content
include 'place_order.html';
?>