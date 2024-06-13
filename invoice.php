<?php
require 'config.php';

// Include autoload.inc.php of Dompdf package
require __DIR__ . "/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// Get customer record
$sql = "SELECT * FROM users_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$userRow = $result->fetch_assoc();

// Get order count
$sql = "SELECT COUNT(*) FROM order_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalRows = $row['COUNT(*)'];

// Get total sales and quantity
$sql = "SELECT SUM(Quantity) as total_quantity FROM order_table WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderRow['User_ID']);
$stmt->execute();
$result = $stmt->get_result();
$quantity = $result->fetch_assoc();

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
            font-family: Times Newroman;
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
    <h1 style="font-size: 30px; text-align: right;">Dilthira<br>Associats</h1>
    </td>
    </tr>
    </table>
    <hr width="100%">
    <h1>Invoice for ' . $userRow['F_Name'] . ' ' . $userRow['L_Name'] . '</h1>
    <table width="100%">
    <tr>
    <th>Customer ID</th>
    <th>Date</th>
    <th>Quantity</th>
    <th>Stock Cost</th>
    <th>Delivery Charge</th>
    </tr>
    <tr>
    <td align="center">' . $orderRow['User_ID'] . '</td>
    <td align="center">' . $orderRow['Date'] . '</td>
    <td align="center">' . $orderRow['Quantity'] . '</td>
    <td align="center">' . $orderRow['Price_Rs'] . '.Rs</td>
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
    <li>Customer ID: ' . $orderRow['User_ID'] . '</li>
    <li>Previouse order count: ' . $totalRows . '</li>
    <li>Total stocks acquired : ' . $quantity['total_quantity'] . '</li>
    </ul>
    <br>
    <table width=100% align="center">
    <tr>
    <td align="center" style="">...........................<br>
    Manager<br>'
    . $currentDate . '
    </td>
    <td align="center">...........................<br>
    Delivery<br>'
    . $currentDate . '
    </td>
    <td align="center">...........................<br>'
    . $userRow['F_Name'] . ' ' . $userRow['L_Name'] . '<br>
    Customer<br>'
    . $currentDate . '
    </td>
    </tr>
</body>
</html>
';

// Load content to the pdf
$pdf->loadHtml($html);

// Set up the paper size and orientation
$pdf->setPaper('A4', 'landscape');

// Generate pdf
$pdf->render();

$pdf->addInfo("Title", "Monthly Report");

// Load it to the browser
$pdf->stream("invoice_" . date('Y-m-d_H-i-s') . ".pdf", array("Attachment" => false));

$output = $pdf->output();
file_put_contents("file_invoice.pdf", $output);
?>
