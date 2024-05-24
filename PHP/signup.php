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
            echo "<script>alert('Username or Email already exists.')</script>";
        } else {
            // Validate password
            if (empty($password)) {
                echo "<script>alert('Password is required.')</script>";
            } elseif (strlen($password) < 8) {
                echo "<script>alert('Password must be at least 8 characters.')</script>";
            } elseif (!preg_match("/[a-z]/i", $password)) {
                echo "<script>alert('Password must contain at least one letter.')</script>";
            } elseif (!preg_match("/[0-9]/", $password)) {
                echo "<script>alert('Password must contain at least one number.')</script>";
            } elseif ($password !== $rt_pass) {
                echo "<script>alert('Passwords do not match.')</script>";
            } else {
                // Create an encrypted password
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                require 'otp_verification.php';

                // // Query execution
                // $stmt = $conn->prepare("INSERT INTO users_table (Email, F_Name, L_Name, U_Name, NIC, Tel_No, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                // $stmt->bind_param("sssssss", $email, $fname, $lname, $uname, $nic, $telNo, $pass_hash);

                // if ($stmt->execute()) {
                //     echo "<script>alert('Sign-up Success!')</script>";
                // } else {
                //     echo "<script>alert('Error occurred during sign-up.')</script>";
                // }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <div class="mx-auto h-screen w-full sm:w-1/2 right-0 bg-white opacity-80 absolute items-center justify-center">
        <div class="image">
            <img src="Styles/Images/p3.png" alt="logo">
        </div>
        <div class="sm:text-sm" style="font-family:'Inlander-Smooth';">
            Sign Up
        </div>
        <form action="" method="post" novalidate>
            <input type="email" name="email" placeholder="Email" required><br>
            <br><input type="text" name="fname" placeholder="First Name"><br>
            <br><input type="text" name="lname" placeholder="Last Name"><br>
            <br><input type="text" name="uname" placeholder="User Name"><br>
            <br><input type="text" name="nic" placeholder="NIC" required><br>
            <br><input type="text" name="telNo" placeholder="Telephone"><br>
            <br><input type="password" name="password" placeholder="Password" required><br>
            <br><input type="password" name="rt_password" placeholder="Retype Password" required><br>
            <input type="submit" name="submit" value="Sign Up">
        </form>
        <span>Already have an account? <a href="login.php">Login</a></span>
    </div>
</body>
</html>
