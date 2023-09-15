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
require_once '/xampp/htdocs/foodie/asset/modules/mail.php'; // Include the User class file

// Assuming UserLogin class is defined and available
$utility=new Utility();

$result=User::userForgotPword($data);

if ($result === true) {
    $utility->response(true, 'Password sent to $data->mail', null);
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $utility->response(false, 'Password not sent to $data->mail', null);
}
?>
