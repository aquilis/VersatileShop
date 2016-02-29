<?php
include '../lib/utils.php';
include "../dataAccessObjects/OrdersDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$ordersDAO = new OrdersDAO("orders");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    if (isset($_GET["username"])) {
        $username = filter_input(INPUT_GET, "username", FILTER_SANITIZE_STRING);
        $jsonData = $ordersDAO->getOrdersHistoryForUsername($username);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
} else if ($requestMethod == "POST") {
    //implement the POST handler here
}
?>