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

if (!empty($data)) {
    $updated_features = Restaurant::editRestaurantData($data);

    if ($updated_features) {
        $utility->response(true, "Restaurant information updated successfully.", $updated_features);
    } else {
        $utility->response(false, "Error updating restaurant information.", null);
    }
} else {
    $utility->response(false, "Invalid data.", null);
}


?>