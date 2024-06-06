<?php
require 'config.php'; // Assuming config.php has your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    // Check if a session is already active (optional, might be redundant)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Access user ID from the session
    $id = $_SESSION["id"];

    // Setting user inputs to variables
    $organization = htmlspecialchars($_POST["organization"]);
    $quantity = (double)$_POST["quantity"];
    $address1 = htmlspecialchars($_POST["address1"]);
    $address2 = htmlspecialchars($_POST["address2"]);
    $recite = $_FILES["recite"];
    $price = $quantity * 2700;

    // Getting available stock
    $available_stock = isset($_SESSION["available_stock"]) ? $_SESSION["available_stock"] : 0;
    $errorMsg = "";

    // Input validation
    if (empty($organization)) {
        $errorMsg = "Please enter the organization name.";
    } else if ($quantity <= 0) {
        $errorMsg = "Please enter a valid quantity (positive number).";
    } else if ($quantity > $available_stock) {
        $errorMsg = "Not enough stock available. Current stock: $available_stock kg.";
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

                $sql = "INSERT INTO order_table (User_ID, Date, Quantity, Price_Rs, Recite, Add_Line1, Add_Line2, needs_verification) VALUES ($id, CURDATE(), $quantity, $price, '$targetFile', '$organization, $address1', '$address2', TRUE)";
                
                try {
                    // Execute query
                    if ($conn->query($sql) === TRUE) {
                        $_SESSION["available_stock"] -= $quantity; // Deduct stock
                        echo "<script>alert('Order placed successfully! Your order ID is: " . $conn->insert_id . "');window.location.href = 'index.php';</script>";
                        exit();
                    } else {
                        $errorMsg = "Error: " . $conn->error;
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const quantityInput = document.getElementById('txtqt');
            const priceDisplay = document.getElementById('priceDisplay');

            // Dynamically displaying the price as quantity changes
            quantityInput.addEventListener('input', function() {
                const quantity = parseInt(this.value);
                const price = quantity * 2700;
                priceDisplay.textContent = price;
            });
        });
    </script>
    <style>
        #msg {
            animation: cssAnimation 0s ease-in 2s forwards;
            animation-fill-mode: forwards;
        }

        @keyframes cssAnimation {
            to {
                width: 0;
                height: 0;
                overflow: hidden;
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    <h1>Place Order</h1>
    <!-- Display Message -->
    <?php if (isset($successMsg)) : ?>
        <p style="color: green;"><?php echo $successMsg; ?></p>
    <?php endif; ?>

    <?php if (isset($errorMsg)) : ?>
        <p id="msg" style="color: red;"><?php echo $errorMsg; ?></p>
    <?php endif; ?>

    <!-- Order detail form -->
    <form id="orderForm" action="" method="post" enctype="multipart/form-data">
        <label for="organization">Organization</label>
        <input type="text" name="organization" id="txtorg" required><br><br>
        <label for="quantity">Quantity (kg)</label>
        <input type="number" name="quantity" id="txtqt" min="1" max="<?php echo $available_stock; ?>" required><br><br>
        <label for="address1">Address Line 1</label>
        <input type="text" name="address1" id="txtadd1" required><br><br>
        <label for="address2">Address Line 2</label>
        <input type="text" name="address2" id="txtadd2" required><br><br>
        <label for="recite">Recite</label>
        <input type="file" name="recite" id="imprecite" accept=".jpg,.jpeg,.png" required><br><br>
        <label for="price">Price (Rs.):</label>
        <span id="priceDisplay">0</span><br><br>
        <input type="submit" name="submit" value="Submit">
    </form>
    <script>
        const orderForm = document.getElementById('orderForm');
        const quantityInput = document.getElementById('txtqt');
        const availableStock = <?php echo $available_stock; ?>;

        // Limiting customer quantity
        orderForm.addEventListener('submit', function(event) {
            const quantity = parseInt(quantityInput.value);
            if (quantity > availableStock) {
                event.preventDefault();
                alert("Not enough stock available. Current stock: " + availableStock + " kg.");
            }
        });
    </script>
</body>
</html>
