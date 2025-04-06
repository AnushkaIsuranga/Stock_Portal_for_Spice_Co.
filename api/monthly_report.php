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

// Setting up options
$options = new Options();
$options -> setChroot(__DIR__);
$options -> isRemoteEnabled(true);

// Creating new instance
$pdf = new Dompdf($options);

// Get selected month and year from the form
if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
} else {
    echo "Month and year are required.";
    exit();
}

// Get total sales and quantity
$sql = "SELECT SUM(Quantity) as total_quantity, SUM(Price_Rs) as total_sales FROM order_table WHERE MONTH(Date) = ? AND YEAR(Date) = ? AND needs_verification = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$sales_data = $result->fetch_assoc();

// Get total orders
$sql = "SELECT COUNT(*) as total_orders FROM order_table WHERE MONTH(Date) = ? AND YEAR(Date) = ? AND needs_verification = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$order_data = $result->fetch_assoc();

// Get stock data
$sql = "SELECT available_stock FROM stock_table LIMIT 1";
$result = $conn->query($sql);
$stock_data = $result->fetch_assoc();

// Calculating financial overview 
$cost_per_kg = 2700;
$profit = $sales_data['total_sales'] - ($sales_data['total_quantity'] * $cost_per_kg);

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
    <h1>Monthly Report for ' . htmlspecialchars(date('F Y', mktime(0, 0, 0, $month, 10, $year))) . '</h1>
    <h2>Executive Summary</h2>
    <ul>
    <li>Total Orders: ' . htmlspecialchars($order_data['total_orders']) . '</li>
    <li>Total Quantity Sold: ' . htmlspecialchars($sales_data['total_quantity']) . ' kg</li>
    <li>Total Sales: Rs. ' . htmlspecialchars(number_format($sales_data['total_sales'], 2)) . '</li>
    </ul>
    <h2>Sales Performance</h2>
    <div style="border: 1px solid black; padding-left: 10px; padding-right: 10px; display: block;">
    <p><b>Details of sales performance...</b></p>
    <p>.......................................................................................................................................................<br>.......................................................................................................................................................<br>.......................................................................................................................................................<br>.......................................................................................................................................................<br>.......................................................................................................................................................<br>.......................................................................................................................................................</p>
    </div>
    <h2>Stock Inventory Update</h2>
    <ul>
    <li>Available Stock: ' . htmlspecialchars($stock_data['available_stock']) . ' kg</li>
    </ul>
    <h2>Financial Overview</h2>
    <ul>
    <li>Total Sales: Rs. ' . htmlspecialchars(number_format($sales_data['total_sales'], 2)) . '</li>
    <li>Total Cost: Rs. ' . htmlspecialchars(number_format($sales_data['total_quantity'] * $cost_per_kg, 2)) . '</li>
    <li>Profit: Rs. ' . htmlspecialchars(number_format($profit, 2)) . '</li>
    </ul>
</body>
</html>
';

// Load content to the pdf
$pdf -> loadHtml($html);

// Set up the paper size and orientation
$pdf -> setPaper('A4', 'portrait');

// Generate pdf
$pdf -> render();

$pdf -> addInfo("Title", "Monthly Report");

// Load it to the browser
$pdf -> stream("monthly_report_" . date('F_Y', mktime(0, 0, 0, $month, 10, $year)) . ".pdf", array("Attachment" => false));

$output = $pdf -> output();
file_put_contents("file_report.pdf", $output)
?>