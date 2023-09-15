<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: GET");
$data = file_get_contents('php://input');
$data = json_decode($data);


require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/menu.php';
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';


// Assuming UserLogin class is defined and available

$utility = new Utility();
Menu::set();

$menuDetails = Menu::getMenuDetail($data->restId);

if ($menuDetails !== false) {
    $utility->response(true, "Menu details retrieved successfully", $menuDetails);
    
} else {
    $utility->response(false, "Menu details not available", null);
}

