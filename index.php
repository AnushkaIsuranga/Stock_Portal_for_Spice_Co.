<?php
    require 'config.php';

    // Check if a session is already active before starting a new one
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Checks if the user is not authorized
    if (!isset($_SESSION["id"])) {
        header("Location: login.php");
        exit();
    }

    $id = $_SESSION["id"];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users_table WHERE User_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_row = $result->fetch_assoc();
    $stmt->close();

    // Store the user data in a session variable for use in the HTML file
    $_SESSION['user_name'] = htmlspecialchars($user_row['U_Name']);

    // Include the HTML file
    include 'index.html';
?>