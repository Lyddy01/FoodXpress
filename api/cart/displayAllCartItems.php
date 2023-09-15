<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: GET"); // Allow only GET requests

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/cart.php';

// Assuming Cart class is defined and available
$utility = new Utility();
Cart::set();

if (isset($data)) {
    $userId = $data->userId;

    // Call the displayCart function
    Cart::displayCart($userId);
} else {
    $utility->response(false, "Invalid input data.", null);
}


?>