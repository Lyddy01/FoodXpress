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

// Assuming UserLogin class is defined and available
$utility = new Utility();
Cart::set();

if (isset($data->menuId) && isset($data->userId)) {
    $menuId = $data->menuId;
    $userId = $data->userId;

    // Call the removeItem function
    Cart::removeItem($data);
} else {
    $utility->response(false, "Invalid input data.", null);
}


?>