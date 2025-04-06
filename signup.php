<?php
require 'config.php';

// Initialize error message variable
$errorMsg = '';

// If submit button is selected
if (isset($_POST["submit"])) {
    // Validate and sanitize user inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING);
    $nic = filter_input(INPUT_POST, 'nic', FILTER_SANITIZE_STRING);
    $telNo = filter_input(INPUT_POST, 'telNo', FILTER_SANITIZE_STRING);
    $password = $_POST["password"];
    $rt_pass = $_POST["rt_password"];

    // Validate required fields
    if (empty($email) || empty($fname) || empty($uname) || empty($nic) || empty($telNo) || empty($password)) {
        $errorMsg = "All fields are required.";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email format.";
        }
        // Validate NIC format (basic check for Sri Lankan NIC)
        elseif (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
            $errorMsg = "Invalid NIC format. Please enter a valid 9-digit (with V/X) or 12-digit NIC number.";
        }
        // Validate telephone number (Sri Lankan format)
        elseif (!preg_match('/^[0-9]{9,10}$/', $telNo)) {
            $errorMsg = "Invalid telephone number. Please enter a 9 or 10 digit number.";
        }
        // Check if the email or username exists 
        else {
            $stmt = $conn->prepare("SELECT * FROM users_table WHERE U_Name = ? OR Email = ?");
            if (!$stmt) {
                $errorMsg = "Database error. Please try again later.";
            } else {
                $stmt->bind_param("ss", $uname, $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $errorMsg = "Username or Email already exists.";
                } else {
                    // Validate password strength
                    if (strlen($password) < 8) {
                        $errorMsg = "Password must be at least 8 characters.";
                    } 
                    elseif (!preg_match("/[a-z]/i", $password)) {
                        $errorMsg = "Password must contain at least one letter.";
                    } 
                    elseif (!preg_match("/[0-9]/", $password)) {
                        $errorMsg = "Password must contain at least one number.";
                    } 
                    elseif ($password !== $rt_pass) {
                        $errorMsg = "Passwords do not match.";
                    } 
                    else {
                        // Create an encrypted password
                        $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                        // Prepare telephone number with country code
                        $formattedTelNo = '+94' . ltrim($telNo, '0');

                        // Insert user into database
                        $stmt = $conn->prepare("INSERT INTO users_table (Email, F_Name, L_Name, U_Name, NIC, Tel_No, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            $errorMsg = "Database error. Please try again later.";
                        } else {
                            $stmt->bind_param("sssssss", $email, $fname, $lname, $uname, $nic, $formattedTelNo, $pass_hash);

                            if ($stmt->execute()) {
                                // Success - redirect to login page
                                header("Location: login.php?signup=success");
                                exit();
                            } else {
                                $errorMsg = "Error occurred during sign-up: " . $conn->error;
                            }
                        }
                    }
                }
            }
        }
    }
}

// Include the HTML template
include 'signup.html';
?>