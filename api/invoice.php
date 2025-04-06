<?php
require 'config.php';

// Include autoload of Dompdf package
require __DIR__ . "/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure $id is set in the session
if (!isset($_SESSION["id"])) {
    die("User ID not set in session.");
}

$id = $_SESSION["id"];
$sms = "invoice_request";
require 'sendSMS.php';

// Setting up options
$options = new Options();
$options->setChroot(__DIR__);
$options->isRemoteEnabled(true);

// Creating new instance
$pdf = new Dompdf($options);

// Get selected order ID from the form
if (isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];
} else {
    echo "Order ID is required.";
    exit();
}

// Get order record
$sql = "SELECT * FROM order_table WHERE Order_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();
$orderRow = $result->fetch_assoc();
$stmt->close();

if (!$orderRow) {
    die("Order not found.");
}

// Get customer record
$sql = "SELECT * FROM users_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$userRow = $result->fetch_assoc();
$stmt->close();

if (!$userRow) {
    die("User not found.");
}

// Get order count
$sql = "SELECT COUNT(*) AS order_count FROM order_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$orderCountRow = $result->fetch_assoc();
$totalOrders = $orderCountRow['order_count'];
$stmt->close();

// Get total sales and quantity
$sql = "SELECT SUM(Quantity) AS total_quantity FROM order_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$quantityRow = $result->fetch_assoc();
$totalQuantity = $quantityRow['total_quantity'];
$stmt->close();

// Get current date
$currentDate = date('Y-m-d');

// Load HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: Arial, sans-serif; 
        }
        h1 { 
            font-family: Times New Roman;
            text-align: center; 
            font-size: 35px;
        }
        h2 {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <table width="100%" style="margin-bottom: 10px;">
        <tr>
            <td>
                <img src="Styles/Images/p3.jpg" alt="logo" height="100px" align="left">
            </td>
            <td align="right">
                <h1 style="font-size: 30px; text-align: right;">Dilthira<br>Associates</h1>
            </td>
        </tr>
    </table>
    <hr width="100%">
    <h1>Invoice for ' . htmlspecialchars($userRow['F_Name']) . ' ' . htmlspecialchars($userRow['L_Name']) . '</h1>
    <table width="100%">
        <tr>
            <th>Customer ID</th>
            <th>Date</th>
            <th>Quantity</th>
            <th>Stock Cost</th>
            <th>Delivery Charge</th>
        </tr>
        <tr>
            <td align="center">' . htmlspecialchars($orderRow['User_ID']) . '</td>
            <td align="center">' . htmlspecialchars($orderRow['Date']) . '</td>
            <td align="center">' . htmlspecialchars($orderRow['Quantity']) . '</td>
            <td align="center">' . htmlspecialchars($orderRow['Price_Rs']) . ' Rs</td>
            <td align="center">.....................</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">Total</td>
            <td align="center">.....................</td>
        </tr>
    </table>
    <h2>Performance</h2>
    <div style="border: 1px solid black; padding-left: 10px; padding-right: 10px; display: block;">
        <p><b>Leave a comment...</b></p>
        <p>..................................................................................................................................................................................................................................<br>..................................................................................................................................................................................................................................
    </div>
    <h2>Financial Overview</h2>
    <ul>
        <li>Customer ID: ' . htmlspecialchars($orderRow['User_ID']) . '</li>
        <li>Previous order count: ' . htmlspecialchars($totalOrders) . '</li>
        <li>Total stocks acquired: ' . htmlspecialchars($totalQuantity) . '</li>
    </ul>
    <br>
    <table width="100%" align="center">
        <tr>
            <td align="center">...........................<br>
                Manager<br>' . htmlspecialchars($currentDate) . '
            </td>
            <td align="center">...........................<br>
                Delivery<br>' . htmlspecialchars($currentDate) . '
            </td>
            <td align="center">...........................<br>' . htmlspecialchars($userRow['F_Name']) . ' ' . htmlspecialchars($userRow['L_Name']) . '<br>
                Customer<br>' . htmlspecialchars($currentDate) . '
            </td>
        </tr>
    </table>
</body>
</html>
';

// Load content to the PDF
$pdf->loadHtml($html);

// Set up the paper size and orientation
$pdf->setPaper('A4', 'landscape');

// Generate PDF
$pdf->render();

// Add document info
$pdf->addInfo("Title", "Monthly Report");

// Load it to the browser
$pdf->stream("invoice_" . date('Y-m-d_H-i-s') . ".pdf", array("Attachment" => false));

// Save PDF to a file
$output = $pdf->output();
file_put_contents("file_invoice.pdf", $output);
?>
