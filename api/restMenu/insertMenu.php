<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/menu.php';


// Assuming UserLogin class is defined and available

$utility = new Utility();
Menu::set();

if (!empty($data)) {
     
    $insertResult = Menu::insertMenu($data);
    

    if ($insertResult === true) {
        $utility->response(true, "Menu inserted successfully", null);
    } else {
        $utility->response(false, "Menu insertion failed", $insertResult);
    }
} else {
    $utility->response(false, "Invalid input data", null);
}
