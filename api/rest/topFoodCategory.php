<?php

// API endpoint code
error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: GET");

require_once '/xampp/htdocs/foodie/asset/modules/databaseConnection.php';
require_once '/xampp/htdocs/foodie/asset/modules/utility.php';
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';

$utility = new Utility();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $highestLikedCategory = Restaurant::getFoodCategoryWithHighestLikes();

    if (!empty($highestLikedCategory)) {
        $utility->response(true, "Food category with highest likes fetched successfully.", $highestLikedCategory);
    } else {
        $utility->response(false, "No food category found.", null);
    }
} else {
    $utility->response(false, "Invalid request method.", null);
}


?>