<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once  '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php'; // Include the User class file


$utility=new Utility();


    // Check if the request is a POST request
    if ($data) {
        // Validate and extract parameters

        $userLat = isset($data->userLat) ? floatval($data->userLat) : null;
        $userLon = isset($data->userLon) ? floatval($data->userLon) : null;
        $restLat = isset($data->restLat) ? floatval($data->restLat) : null;
        $restLon = isset($data->restLon) ? floatval($data->restLon) : null;

        if ($userLat !== null && $userLon !== null && $restLat !== null && $restLon !== null) {
            // Calculate distance
           if( $distance = Restaurant::haversineDistance($userLat, $userLon, $restLat, $restLon)){
                 $utility->response(true,"the distance for the user to a nearby restaurant is about  $distance km",$distance);
           }else{
            $utility->response(false,"error in calculation",null);

           }
    
        } else {
            // Invalid or missing parameters
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid or missing parameters']);
        }
    } else {
        // Invalid JSON data
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid JSON data']);
    }


?>
