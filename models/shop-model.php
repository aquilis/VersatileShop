<?php
include '../lib/db_functions.php';

//get the database connection
$dbConnection = getVersatileShopDbConnection();

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
        //get the basic product details
        $query = "SELECT  productID, title, description, price, manufacturer, dateAdded FROM products WHERE productID=" . mysqli_real_escape_string($dbConnection, $productID) . " LIMIT 1";
        $result = mysqli_query($dbConnection, $query);
        //if a product with that ID does not exist, just return an empty json array and terminate the function
        if(mysqli_num_rows($result) == 0) {
            returnEmptyJsonArray();
            return;
        }
        //if the product was found, construct the response and fetch all product attributes from the related tables
        $row = mysqli_fetch_array($result, MYSQL_NUM);
        $jsonData = array();
        $jsonData['productID'] = $row[0];
        $jsonData['title'] = $row[1];
        $jsonData['description'] = $row[2];
        $jsonData['price'] = $row[3];
        $jsonData['manufacturer'] = $row[4];
        $jsonData['dateAdded'] = $row[5];
        //get the images for this product from the images table and add them to the response
        $query = "SELECT imagePath, imageDescription FROM images where productID=" . mysqli_real_escape_string($dbConnection, $productID);
        $result = mysqli_query($dbConnection, $query);
        $jsonData['images'] = array();
        $rowArray = array();
        while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
            $rowArray['imagePath'] = $row[0];
            $rowArray['imageDescription'] = $row[1];
            array_push($jsonData['images'], $rowArray);
        }
        //get the videos for this product from the videos table and add them to the response
        $query = "SELECT videoSrc, videoCaption FROM videos where productID=" . mysqli_real_escape_string($dbConnection, $productID);
        $result = mysqli_query($dbConnection, $query);
        $jsonData['videos'] = array();
        $rowArray = array();
        while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
            $rowArray['videoSrc'] = $row[0];
            $rowArray['videoCaption'] = $row[1];
            array_push($jsonData['videos'], $rowArray);
        }
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else {
        //return all products (the most basic details only) for listing purpose
        $query = "SELECT productID, title, SUBSTRING(description,1,220), price, thumbnailPath FROM products WHERE available=true order by dateAdded desc";
        $result = mysqli_query($dbConnection, $query);

        $jsonData = array();
        while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
            $rowArray['productID'] = $row[0];
            $rowArray['title'] = $row[1];
            $rowArray['description'] = $row[2];
            $rowArray['price'] = $row[3];
            $rowArray['thumbnailPath'] = $row[4];
            array_push($jsonData, $rowArray);
        }
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
} else if ($requestMethod == "POST") {
    //implement the POST handler here
}
?>