<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once  '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php';
require_once '/xampp/htdocs/foodie/asset/modules/use.php'; // Include the User class file

  $utility = new Utility();

    // Check if the user ID is provided in the query string
    if (isset($data)) {
        
        $closestRestaurants = Restaurant::getNearByRestaurant($data->userId);

        // Prepare response
        $response = [];
        foreach ($closestRestaurants as $restaurant) {
            $response[] = [
                'id' => $restaurant['id'],
                'name' => $restaurant['name'],
                'address' => $restaurant['address'],
                'phone' => $restaurant['phone'],
                'distance' => $restaurant['distance'],
            ];
        }

        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'User ID is missing']);
    }



?>