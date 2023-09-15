<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");

$data = file_get_contents('php://input');
$data = json_decode($data);


require_once  '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php'; // Include the User class file
require_once '/xampp/htdocs/foodie/asset/modules/rest.php'; // Include the User class file


$utility = new Utility();

        

// Check if $data is set and not empty
if (!isset($data) || empty($data)) {
    $utility->response(false, "Invalid parameter", "");
} else {
    $name = $utility->validateSignUp($data->name);
    $mail = $utility->validateEmail($data->mail);
    $phone = $utility->validateNumbers($data->phone);
    $pword = $utility->validateSignUp($data->pword);
    $confpword = $utility->validateSignUp($data->confpword);
    $lon = is_numeric($data->lon) ? null : 'Invalid longitude value.';
    $lat = is_numeric($data->lat) ? null : 'Invalid latitude value.';

    if (!$name) {
        $utility->response(false, "Enter your  Name", "");
    } elseif (!$mail) {
        $utility->response(false, "Enter a valid email", "");
    } elseif (!$phone) {
        $utility->response(false, "Enter a valid phone number", "");
    } elseif (!$pword) {
        $utility->response(false, "Enter your password", "");
    } elseif (!$confpword) {
        $utility->response(false, "Confirm your password", "");
    } elseif (strlen($pword) < 8 || strlen($confpword) < 8) {
        $utility->response(false, "Password should be 8+ characters", "");
    } elseif ($pword !== $confpword) {
        $utility->response(false, "Passwords don't match", "");
    } else {
        $check= Restaurant::checkIfRestaurantMailExists($data->mail);
        if ($check === true) {
            $utility->response(false, "This email is already in use", "");
        } else {
            if ($registrationResult = Restaurant::registerRestaurant($data)) {
                http_response_code(201);
                $utility->response(true, "Registration successful. Please login.", $registrationResult);
            } else {
                $utility->response(false, "Registration unsuccessful", "");
            }
        }
    }
}

?>
