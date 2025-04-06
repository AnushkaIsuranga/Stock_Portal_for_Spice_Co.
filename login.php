<?php
require 'config.php';
session_start(); // Start the session at the beginning

if (isset($_POST["submit"])) {
    //Setting user inputs to variables
    $name_email = $_POST["name_email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users_table WHERE U_Name = ? OR Email = ?");
    $stmt->bind_param("ss", $name_email, $name_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row is selected
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedHash = $row["Password"];

        // Verify the password
        if (password_verify($password, $storedHash)) {
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["User_ID"];
            $_SESSION["role"] = $row["Role"];

            // Redirect based on the role
            if ($row["Role"] === 'admin') {
                header("Location: admin.php");
            } elseif ($row["Role"] === 'manager') {
                header("Location: manager.php");
            } else {
                header("Location: index.php");
            }
            exit(); 
        } else {
            $errorMsgPass = "Wrong password. Please try again.";
        }
    } else {
        // Check hardcoded credentials for admin and manager
        if (($name_email === 'admin' || $name_email === 'admin@123.com') && $password === 'admin.123') {
            $_SESSION["login"] = true;
            $_SESSION["role"] = 'admin';
            header("Location: admin.php");
            exit();
        } elseif (($name_email === 'manager' || $name_email === 'manager@123.com') && $password === 'manager.123') {
            $_SESSION["login"] = true;
            $_SESSION["role"] = 'manager';
            header("Location: manager.php");
            exit();
        } else {
            $errorMsgEmail = "Your username/email is incorrect. Please try again.";
        }
    }

    // Close statement
    $stmt->close();
}

// Include the HTML file
include 'login.html';
?>
