<?php
require 'config.php';

// Ensure you start the session to access session variables
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

require __DIR__ . "/vendor/autoload.php";

// Check if $id is set in the session
if (!isset($_SESSION["id"])) {
    die("User ID not set in session.");
}

$id = $_SESSION["id"];

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users_table WHERE User_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$stmt->close();

if (!$user_row) {
    die("User not found.");
}

// Mobile numbers
$managerNumber = "+94752177070";
$adminNumber = "+94771732201";
$cusNumber = $user_row['Tel_No'];

// API credentials
$base_url = "https://6gk82e.api.infobip.com";
$api_key = "40b6b71e62a811e634cdcf974857624b-a3e2fbc1-3815-48ac-9e88-8beabbf21cf8";

// Get current date
$currentDate = date('Y-m-d');

// Ensure $sms is set
if (!isset($sms)) {
    die("SMS type not specified.");
}

// Messages that needed to be sent
switch ($sms) {
    case "order_placed":
        $numbers = [$managerNumber];
        $messageText = 'A customer has placed an order.
        Customer Name: ' . $user_row['U_Name'] . '
        Customer ID: ' . $user_row['User_ID'] . '
        Date: ' . $currentDate;
        break;
    case "stock_limited":
        $numbers = [$managerNumber];
        $messageText = 'Current pepper stock is limited. Please refill.
        Current Stock: ' . $stock_data['available_stock'];
        break;
    case "out_of_stock":
        $numbers = [$managerNumber];
        $messageText = 'Sir, your stock is empty. Please refill.
        ' . $currentDate;
        break;
    case "order_verified":
        $numbers = [$cusNumber, $adminNumber];
        $messageText = 'Your order has been verified by Dilthira Associates. Please be patient, your order will be at your doorstep soon. Thank you!';
        break;
    case "invoice_request":
        $numbers = [$managerNumber];
        $messageText = 'An invoice has been requested.
        Customer Name: ' . $user_row['U_Name'] . '
        Customer ID: ' . $user_row['User_ID'] . '
        Date: ' . $currentDate;
        break;
    default:
        die("Invalid SMS type.");
}

// API configuration
$configuration = new Configuration(host: $base_url, apiKey: $api_key);
$api = new SmsApi(config: $configuration);

// Prepare messages
$messages = [];
foreach ($numbers as $number) {
    $destination = new SmsDestination(to: $number);
    $message = new SmsTextualMessage(
        from: "DILTHIRA",
        destinations: [$destination],
        text: $messageText
    );
    $messages[] = $message;
}

$request = new SmsAdvancedTextualRequest(messages: $messages);

// Send SMS
try {
    $response = $api->sendSmsMessage($request);
} catch (Exception $e) {
    echo "<script>alert('Error sending SMS: " . $e->getMessage() . "')</script>";
}
?>
