<?php
error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: GET");

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php';
require_once '/xampp/htdocs/foodie/asset/modules/utility.php';
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';

$utility = new Utility();


$foodCategories = Restaurant::getAllFoodCategories();

if (!empty($foodCategories)) {
    $utility->response(true, "Food categories fetched successfully.", $foodCategories);
} else {
    $utility->response(false, "No food categories found.", null);
}

?>
