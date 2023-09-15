<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST"); // Allow only POST requests

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/cart.php';

// Assuming Cart class is defined and available
$utility = new Utility();
Cart::set();

if (isset($data->userId)) {
    // Call the calculateTotalFees function
    $totalFees = Cart::calculateTotalFees($data);

    echo json_encode($totalFees);
} else {
    $utility->response(false, "Invalid input data.", null);
}

?>