<?php
include '../lib/utils.php';
include "../dataAccessObjects/ProductDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$productDAO = new ProductDAO("products");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    //returns all available titles in the shop (used usually for listing or autocomplete)
    if ((isset($_GET["action"])) && ($_GET["action"] == "allTitles")) {
        $titles = $productDAO->getAllProductTitles();
        $jsonData = array();
        //pack the titles into a simple 1-dimensional array
        foreach ($titles as $key => $value) {
            array_push($jsonData, $value["title"]);
        }
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else if ($_GET["action"] == "search") {
        
        $criteria = array();
        if (isset($_GET["title"])) {
           $criteria["title"] = htmlspecialchars($_GET["title"], ENT_QUOTES);
        }
        if (isset($_GET["description"])) {
            $criteria["description"] = htmlspecialchars($_GET["description"], ENT_QUOTES);
        }

        $data = $productDAO->search($criteria);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
} else if ($requestMethod == "POST") {
    //implement the POST handler here
}
?>