<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once  '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/use.php';
require_once '/xampp/htdocs/foodie/asset/modules/rest.php'; // Include the User class file
require_once('/xampp/htdocs/foodie/asset/modules/mail.php'); // Include the Mailer class


// Assuming UserLogin class is defined and available

 // Create an instance of the UserLogin class
$utility = new Utility();


if (!isset($data)) {
    $utility->response(false, "Invalid parameter", "");
} else {
    $mail = $utility->validateEmail($data->mail);
    $pword = $utility->validateSignUp($data->pword);

    if (!$mail) {
        $utility->response(false, "Enter a valid email", "");
    } elseif (!$pword) {
        $utility->response(false, "Enter your password", "");
    } else {
        $table="restaurants";
        $loginResult = Restaurant::loginRestaurant($data);
        if ($loginResult === true) {
            http_response_code(200);
            $utility->response(true, "Login successful", "");
        } else {
            $utility->response(false, "Login failed. Invalid credentials", "");
        }
    }
}
