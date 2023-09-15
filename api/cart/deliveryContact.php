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


if (isset($data)) {
    // Extract data from the request
    $userId = $data->userId;
    $street_address = $data->street_address;
    $house_number = $data->house_number;
    $phone_number = $data->phone_number;
    $label = $data->label;
    

    // Assuming you have a function to insert delivery details into your database
    $insertResult = Cart::deliveryDetails($data);

    if ($insertResult) {
        $utility->response(true, "Delivery details have been successfully recorded.", null);
    } else {
        $utility->response(false, "Failed to record delivery details.", null);
    }
} else {
    $utility->response(false, "Invalid input data.", null);
}


?>