<?php
require 'config.php';
session_start();

// Fetch new orders that need verification
$sql = "SELECT * FROM order_table WHERE needs_verification = TRUE";
$result = $conn->query($sql);

// Handle order verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["verify_order"])) {
    $orderId = (int)$_POST["order_id"];
    $sql = "UPDATE order_table SET needs_verification = FALSE WHERE Order_ID = $orderId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Order verified successfully!');</script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Welcome, Administrator.</h1>
    <a href="logout.php">Logout</a><br><br>
    <h2>New Orders Needing Verification</h2>
    <div id="orderList">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div>";
                echo "Order ID: " . $row["Order_ID"] . "<br>";
                echo "User ID: " . $row["User_ID"] . "<br>";
                echo "Quantity: " . $row["Quantity"] . "<br>";
                echo "Price: Rs. " . $row["Price_Rs"] . "<br>";
                echo "Address Line 1: " . $row["Add_Line1"] . "<br>";
                echo "Address Line 2: " . $row["Add_Line2"] . "<br>";
                echo "<img src='" . $row["Recite"] . "' alt='Receipt' style='max-width: 200px;'><br>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='order_id' value='" . $row["Order_ID"] . "'>";
                echo "<input type='submit' name='verify_order' value='Verify Order'>";
                echo "</form>";
                echo "</div><br>";
            }
        } else {
            echo "No new orders needing verification.";
        }
        ?>
    </div>
    <script>
        function checkNewOrders() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'check_new_orders.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.new_orders > 0) {
                        alert('You have ' + response.new_orders + ' new orders to verify!');
                        // Optionally reload the page or update the order list dynamically
                        location.reload();
                    }
                }
            };
            xhr.send();
        }

        // Check for new orders every 30 seconds
        setInterval(checkNewOrders, 30000);
    </script>
</body>
</html>
</body>
</html>