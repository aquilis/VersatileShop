<?php
include '../lib/utils.php';
include "../dataAccessObjects/ProductDAO.php";
include "../dataAccessObjects/CategoriesDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$productDAO = new ProductDAO("products");

$categoriesDAO = new CategoriesDAO("categories");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    //returns all available titles in the shop (used usually for listing or autocomplete)
    if ((isset($_GET["action"])) && ($_GET["action"] == "allTitles")) {
        $term = "";
        if(isset($_GET["term"])) {
            $term = htmlspecialchars($_GET["term"], ENT_QUOTES);
        }
        $response = $productDAO->getAllProductTitles($term);
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    } else if (($_GET["action"] == "allCategories")) {
        $term = "";
        if(isset($_GET["term"])) {
            $term = htmlspecialchars($_GET["term"], ENT_QUOTES);
        }
        $response = $categoriesDAO->getAllCategories($term);
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    } else if ($_GET["action"] == "search") {
        
        $criteria = array();
        if (isset($_GET["productID"])) {
            $criteria["productID"] = htmlspecialchars($_GET["productID"], ENT_QUOTES);
        }
        if (isset($_GET["categoryID"])) {
            $criteria["categoryID"] = htmlspecialchars($_GET["categoryID"], ENT_QUOTES);
        }
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