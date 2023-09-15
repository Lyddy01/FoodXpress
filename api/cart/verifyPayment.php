<?php
error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");  // Allow only POST requests

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';
require_once '/xampp/htdocs/foodie/asset/modules/menu.php';
require_once '/xampp/htdocs/foodie/asset/modules/cart.php';


// Initialize Utility class
$utility = new Utility();

    if (isset($data->userId) && isset($data->reference)) {
        // Extract data from the request
        $userId = $data->userId;
        $transactionReference = $data->reference;

        // Call the verifyPayment function from the Cart class
        $verificationResult = Cart::processPayment($data);

        if ($verificationResult === true) {
            $utility->response(true, "Payment verification successful.", null);
        } else {
            $utility->response(false, "Payment verification failed.", null);
        }
    } else {
        $utility->response(false, "Invalid input data.", null);
    }

?>
