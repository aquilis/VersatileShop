<?php
include '../lib/utils.php';
include "../dataAccessObjects/UsersDAO.php";

$usersDAO = new UsersDAO("users");

$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if($action == "getAllUsernames") {
        $term = "";
        if(isset($_GET["term"])) {
            $term = htmlspecialchars($_GET["term"], ENT_QUOTES);
        }
        $response = $usersDAO->getAllUsernames($term);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>