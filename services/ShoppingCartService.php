<?php

include '../lib/utils.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//get the database connection
$dbConnection = getVersatileShopDbConnection();

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

const PRODUCT_REMOVED_FROM_CART = "product.removed.shopping.cart";
const ADDED_TO_CART = "added.to.shopping.cart";
const MORE_ADDED_TO_CART = "more.items.added.to.shopping.cart";
const CART_IS_EMPTY = "shopping.cart.empty";
const LOGIN_REQUIRED = "shopping.cart.login.required";


if ($requestMethod == "GET") {
    if (isset($_SESSION["products"])) {
        $jsonData = array();
        foreach ($_SESSION["products"] as $cartItem) {
            $rowArray["productID"] = $cartItem["productID"];
            $rowArray['title'] = $cartItem["title"];
            $rowArray['price'] = $cartItem["price"];
            $rowArray['thumbnailPath'] = $cartItem["thumbnailPath"];
            $rowArray['quantity'] = $cartItem["quantity"];
            array_push($jsonData, $rowArray);
        }
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else {
        //if the shopping cart is undefiined (non-set), just return an empty array
        returnEmptyJsonArray();
    }
} else if ($requestMethod == "POST") {
    if(!isLogged()) {
        $response = array("message" => LOGIN_REQUIRED, "success" => false);
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
    //only the product ID and the requested quantity are passed via the POST
    $productID = filter_var($_POST["productID"], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($_POST["quantity"], FILTER_SANITIZE_NUMBER_INT);
    $found = false;
    $message = "";
    if (isset($_SESSION["products"])) {
        //iterate over the shopping cart
        foreach ($_SESSION["products"] as $k => $v) {
            if ($v["productID"] == $productID) {
                //the product already exists in the cart, so just increase its quantity
                $_SESSION["products"][$k]["quantity"]+= $quantity;
                $found = true;
                $message = MORE_ADDED_TO_CART;
                break;
            }
        }
    }
    //if a product with that ID doesn't exist yet in the shopping cart, get its details from the DB and add it to the cart
    if ($found == false) {
        $query = "SELECT title, price, thumbnailPath FROM products WHERE productID=" . mysqli_real_escape_string($dbConnection, $productID) . " LIMIT 1";
        $result = mysqli_query($dbConnection, $query);
        $row = mysqli_fetch_array($result, MYSQL_NUM);
        $title = $row[0];
        $price = $row[1];
        $thumbnailPath = $row[2];
        mysqli_close($dbConnection);
        $newProduct = array("productID" => $productID, "title" => $title, "price" => $price, "thumbnailPath" => $thumbnailPath, "quantity" => $quantity);
        if (isset($_SESSION["products"])) {
            array_push($_SESSION["products"], $newProduct);
        } else {
            $_SESSION["products"] = array($newProduct);
        }
        $message = ADDED_TO_CART;
    }
    $response = array("message" => $message, "success" => true);
    header('Content-Type: application/json');
    echo json_encode($response);
} else if ($requestMethod == "DELETE") {
    if (isset($_SESSION["products"])) {
        //If an ID is given to the delete handler, remove that product from the shopping cart
        if (isset($_GET["productID"])) {
            $productID = filter_var($_GET["productID"], FILTER_SANITIZE_NUMBER_INT);
            foreach ($_SESSION["products"] as $k => $v) {
                if ($v["productID"] == $productID) {
                    unset($_SESSION["products"][$k]);
                    echo PRODUCT_REMOVED_FROM_CART;
                    break;
                }
            }
        } else {
            //if no specific product ID is given, reset the whole shopping cart
            $_SESSION["products"] = [];
            echo CART_IS_EMPTY;
        }
    }
}
?>