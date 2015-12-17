<?php
include '../lib/utils.php';
include "../dataAccessObjects/ProductDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$productDAO = new ProductDAO("products");

$categoriesDAO = new ProductDAO("categories");

$imagesDAO = new ImagesDAO("images");

$videosDAO = new VideosDAO("videos");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if($action == "allCategories") {
        $jsonData = $categoriesDAO->getAll();
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
} else if ($requestMethod == "POST") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    
    if($action == "create") {
        $productData = Array();
        $productData["categoryID"] = $_POST["categoryID"];
        $productData["title"] = $_POST["title"];
        $productData["description"] = $_POST["description"];
        $productData["price"] = $_POST["price"];
        $productData["available"] = 1;
        $productData["dateAdded"] = date('Y-m-d H:i:s');
        $productData["manufacturer"] = $_POST["manufacturer"];
        $productData["thumbnailPath"] = $_POST["thumbnailPath"];
        //validation goes here
        $productId = $productDAO->save($productData);

        //validation of the images goes here
        if(isset($_POST["pictureOne"])) {
            $image["productID"] = $productId;
            $image["imagePath"] = $_POST["pictureOne"];
            $imagesDAO->save($image);
        }
        if(isset($_POST["pictureTwo"])) {
            $image["productID"] = $productId;
            $image["imagePath"] = $_POST["pictureTwo"];
            $imagesDAO->save($image);
        }
        if(isset($_POST["pictureThree"])) {
            $image["productID"] = $productId;
            $image["imagePath"] = $_POST["pictureThree"];
            $imagesDAO->save($image);
        }
        if((isset($_POST["videoCaption"])) && (isset($_POST["videoURL"]))) {
            $video["productID"] = $productId;
            $video["videoCaption"] = $_POST["videoCaption"];
            $video["videoSrc"] = $_POST["videoURL"];
            $videosDAO->save($video);
        }

        $result["status"] = true;
        $result["message"] = "Product added successfully.";
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>