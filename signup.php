<?php
    require 'config.php';

    // If submit button is selected
    if (isset($_POST["submit"])) {
        // Setting user inputs to variables
        $email = $_POST["email"];
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $uname = $_POST["uname"];
        $nic = $_POST["nic"];
        $telNo = $_POST["telNo"];
        $password = $_POST["password"];
        $rt_pass = $_POST["rt_password"];

        // Check if the email or username exists 
        $stmt = $conn->prepare("SELECT * FROM users_table WHERE U_Name = ? OR Email = ?");
        $stmt->bind_param("ss", $uname, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errorMsg = "Username or Email already exists.";
        } else {
            // Validate password
            if (empty($password)) {
                $errorMsg = "Password is required.";
            } elseif (strlen($password) < 8) {
                $errorMsg = "Password must be at least 8 characters.";
            } elseif (!preg_match("/[a-z]/i", $password)) {
                $errorMsg = "Password must contain at least one letter.";
            } elseif (!preg_match("/[0-9]/", $password)) {
                $errorMsg = "Password must contain at least one number.";
            } elseif ($password !== $rt_pass) {
                $errorMsg = "Passwords do not match.";
            } else {
                // Create an encrypted password
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                // require 'otp_verification.php';

                // Query execution
                $stmt = $conn->prepare("INSERT INTO users_table (Email, F_Name, L_Name, U_Name, NIC, Tel_No, Password) VALUES (?, ?, ?, ?, ?, CONCAT('+94', ?), ?)");
                $stmt->bind_param("sssssss", $email, $fname, $lname, $uname, $nic, $telNo, $pass_hash);

                if ($stmt->execute()) {
                    echo "<script>alert('Sign-up Success!'); window.location.href = 'login.php';</script>";

                } else {
                    $errorMsg = "Error occurred during sign-up.";
                }
            }
        }
    }
    include 'signup.html'
?>