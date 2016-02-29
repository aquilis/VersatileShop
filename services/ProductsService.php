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
        
        $validationResult = validateRegistration($productData, $dbConnection);
        //if the validation has failed, return the status and error and terminate the function.
        if ($validationResult["status"] == false) {
            header('Content-Type: application/json');
            echo json_encode($validationResult);
            return;
        }

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


function validateRegistration($productData, $dbConnection) {
    $categoryID = $productData["categoryID"];
    $title = $productData["title"];
    $description = $productData["description"];
    $price = $productData["price"];
    //$available = $productData["available"];
    //$dateAdded = $productData["dateAdded"];
    $manufacturer = $productData["manufacturer"];
    $thumbnailPath = $productData["thumbnailPath"];
    $isValid = true;
    $validationMessage = "";
    // if (!preg_match('/^[0-9]{5,10}$/', $categoryID)) {
    //     $validationMessage .= "categoryID must be a number\n" . PHP_EOL;
    //     $isValid = false;
    // }
    if (!preg_match('/^[a-zA-Z0-9\s\-\.]{3,20}$/', $title)) {
        $validationMessage .= "product.title.validation.fail\n" . PHP_EOL;
        $isValid = false;
    }
    if (!preg_match('/^[a-zA-Z0-9\-\.\,\(\)\/\s]{10,300}$/', $description)) {
        $validationMessage .= "product.description.validation.fail\n" . PHP_EOL;
        $isValid = false;
    }
    if (!preg_match('/^[0-9\.]{1,6}$/', $price)) {
        $validationMessage.= "product.price.validation.fail\n" . PHP_EOL;
        $isValid = false;
    }
    if (!preg_match('/^[a-zA-Z0-9\s\.\-]{3,20}$/', $manufacturer)) {
        $validationMessage .= "product.manufacturer.validation.fail\n" . PHP_EOL;
        $isValid = false;
    }
    if (!preg_match('/^[a-zA-Z0-9\/\.\-]{3,30}$/', $thumbnailPath)) {
        $validationMessage .= "product.thumbnail.path.validation.fail\n" . PHP_EOL;
        $isValid = false;
    }
    //Yes, here should NOT be any 'else'
    //if the mandatory details are not valid, don't validate the additional
    if (!$isValid) {
        $response = array();
        $response["status"] = $isValid;
        $response["message"] = $validationMessage;
        $response["errorInMandatoryFields"] = true;
        return $response;
    }
    //TODO check the non-mandatory fields here
    $response = array();
    $response["status"] = $isValid;
    $response["message"] = $validationMessage;
    $response["errorInMandatoryFields"] = false;
    return $response;
}
?>