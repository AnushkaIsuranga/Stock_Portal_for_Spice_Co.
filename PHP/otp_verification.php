<?php 

require 'config.php';

if (isset($_POST["submit"])) {
    $enterOtp = (int)$_POST['otp'];
    //OTP generation php goes here
    $generatOtp = null;//Generated OTP

    if($enterOtp === $generatOtp){
        // Query execution
        $stmt = $conn->prepare("INSERT INTO users_table (Email, F_Name, L_Name, U_Name, NIC, Tel_No, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $email, $fname, $lname, $uname, $nic, $telNo, $pass_hash);

        if ($stmt->execute()) {
            echo "<script>alert('Sign-up Success!'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error occurred during sign-up.')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify</title>
</head>
<body>
    <h1>Enrer OTP</h1>
    <input type="text" name="otp" id="txtOtp">
</body>
</html>