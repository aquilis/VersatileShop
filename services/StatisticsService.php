<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once '../lib/utils.php';
include_once "../dataAccessObjects/OrdersDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$ordersDAO = new OrdersDAO("orders");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    $statisticsType = filter_input(INPUT_GET, "statisticsType", FILTER_SANITIZE_STRING);
    if($statisticsType == "orders-in-time") {
        $timeInterval = filter_input(INPUT_GET, "period", FILTER_SANITIZE_STRING);
        $response = $ordersDAO->getOrdersGroupedByTime($timeInterval);
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    } else if ($statisticsType == "most-bought-products") {
        $response = $ordersDAO->getMostBoughtProduct();
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }  else if ($statisticsType == "revenue-by-time") {
        $timeInterval = filter_input(INPUT_GET, "period", FILTER_SANITIZE_STRING);
        $response = $ordersDAO->getRevenueByTime($timeInterval);
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
} else if ($requestMethod == "POST") {
}


?>