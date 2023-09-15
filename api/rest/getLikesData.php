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
    if (isset($data->restId) && isset($data->userId) && isset($data->value)) {
        $restId = $data->restId;
        $userId = $data->userId;
        $value = $data->value;

        $likeResponse = Restaurant::toggleLikeDislike($restId, $userId, $value);

        if ($likeResponse["status"] === "liked") {
            $utility->response(true, "Restaurant liked.", $likeResponse);
        } elseif ($likeResponse["status"] === "disliked") {
            $utility->response(true, "Restaurant disliked.", $likeResponse);
        } elseif ($likeResponse["status"] === "removed") {
            $utility->response(true, "Like/dislike removed.", $likeResponse);
        } else {
            $utility->response(false, "Error updating like/dislike.", null);
        }
    } else {
        $utility->response(false, "Missing required parameters.", null);
    }
} else {
    $utility->response(false, "Invalid data.", null);
}
?>
