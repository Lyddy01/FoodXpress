<?php
error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php';
require_once '/xampp/htdocs/foodie/asset/modules/utility.php';
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';

$utility = new Utility();

$restaurantInfo = Restaurant::getRestaurantsWithLikes();

$utility->response(true, "Restaurant information fetched successfully.", $restaurantInfo);

?>
