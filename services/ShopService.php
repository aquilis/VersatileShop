<?php
include '../lib/utils.php';
include "../dataAccessObjects/ProductDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$productDAO = new ProductDAO("products");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    if (isset($_GET["productID"])) {
        $productID = filter_input(INPUT_GET, "productID", FILTER_SANITIZE_STRING);
        //if the user has somehow provided a non-numeric ID, return an empty array and terminate the function
        if(!is_numeric($productID)) {
            returnEmptyJsonArray();
            return;
        }
        $jsonData = $productDAO->getProduct($productID);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else {
        $jsonData = $productDAO->getProductHeaders();
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
} else if ($requestMethod == "POST") {
    //implement the POST handler here
}
?>