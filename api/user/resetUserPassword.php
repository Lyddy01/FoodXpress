<?php

error_reporting(E_ALL);
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Methods: POST");

$data = file_get_contents('php://input');
$data = json_decode($data);

require_once  '/xampp/htdocs/foodie/asset/modules/databaseConnection.php'; // Include the Db class file
require_once '/xampp/htdocs/foodie/asset/modules/utility.php'; // Include the Utility class file
require_once '/xampp/htdocs/foodie/asset/modules/account.php';
require_once '/xampp/htdocs/foodie/asset/modules/use.php'; // Include the User class file



$utility = new Utility();

if (!empty($data->mail)&&!empty($data->newpword)){
        
    
    $result = User::resetUserPassword($data->mail, $data->newpword);
        
    if ($result===true) {
        // Password reset successful
        $utility->response(true,"Password reset successful",null);
        
    } else {
        // Password reset failed
        $utility->response(false,"Password reset failed",null);
        
    }
     
}else {
    // Invalid request
    header("HTTP/1.1 400 Bad Request");
    $response = array("error" => "Invalid request");
    echo json_encode($response);
}
