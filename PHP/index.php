<?php
    require 'config.php';
    session_start(); // Start the session at the beginning

    // Checks if the user is not authorized
    if (!isset($_SESSION["id"])) {
        header("Location: login.php");
        exit(); // Ensure to exit after redirect
    }

    $id = $_SESSION["id"];
    
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users_table WHERE User_ID = ?");
    $stmt->bind_param("i", $id); // "i" indicates the parameter type is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($row['U_Name']); ?></h1>
    <a href="logout.php">Logout</a>
</body>
</html>
