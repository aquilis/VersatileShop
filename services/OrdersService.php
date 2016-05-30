<?php
include '../lib/utils.php';
include_once '../services/OrderStates.php';
include "../dataAccessObjects/OrdersDAO.php";
include "../dataAccessObjects/AddressesDAO.php";
include "../dataAccessObjects/OrdersProductsDAO.php";
include "../dataAccessObjects/ProductDAO.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$ordersDAO = new OrdersDAO("orders");

$addressesDAO = new AddressesDAO("addresses");

$ordersProductsDAO = new OrdersProductsDAO("orders_products");

$productDAO = new ProductDAO("products");

const INITIAL_ORDER_STATUS = OrderStates::PENDING;

const TIME_INTERVAL_DAY = "time.interval.day";
const TIME_INTERVAL_WEEK = "time.interval.week";
const TIME_INTERVAL_MONTH = "time.interval.month";


//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if (($action == "myActiveOrders") && isLogged()) {
        $jsonData = $ordersDAO->getActiveOrdersFor($_SESSION['username']);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else if (($action == "myEndedOrders") && isLogged()) {
        $jsonData = $ordersDAO->getEndedOrdersFor($_SESSION['username']);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
    else if (($action == "myAllOrders") && isLogged()) {
        $jsonData = $ordersDAO->getAllOrdersFor($_SESSION['username']);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    } else if (($action == "ordersByCriteria") && isLogged() && isset($_SESSION['isAdmin'])) {
        $criteria = array();
        if(isset($_GET["username"])) {
            $criteria["username"] = filter_input(INPUT_GET, "username", FILTER_SANITIZE_STRING);
        }
        if(isset($_GET["dateRange"])) {
            $inputDate= $_GET["dateRange"];
            $finalDate = null;
            if($inputDate == TIME_INTERVAL_DAY) {
                $finalDate = date('Y-m-d', strtotime('-1 day'));
            } else if ($inputDate == TIME_INTERVAL_WEEK) {
                $finalDate = date('Y-m-d', strtotime("-1 week"));
            } else if ($inputDate == TIME_INTERVAL_MONTH) {
                $finalDate = date('Y-m-d', strtotime("-1 months"));
            }
            $criteria["dateFrom"] = $finalDate;
        }
        if(isset($_GET["status"])) {
            $criteria["status"] = filter_input(INPUT_GET, "status", FILTER_SANITIZE_STRING);
        }
        $data = $ordersDAO->getOrdersByCriteria($criteria);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
} else if ($requestMethod == "POST") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if(!isLogged()) {
        $validationResult = array();
        $validationResult["status"] = false;
        $validationResult["authenticationFailed"] = true;
        header('Content-Type: application/json');
        echo json_encode($validationResult);
        return;
    }
    if($action == "changeStatus") {
        $orderID = filter_input(INPUT_POST, "orderID", FILTER_SANITIZE_STRING);
        $newStatus = filter_input(INPUT_POST, "newStatus", FILTER_SANITIZE_STRING);
        if($newStatus == OrderStates::PENDING) {
            $ordersDAO->update($orderID, array("shippingDate" => "0000-00-00"));
            //$ordersDAO->deleteShippingDate($orderID);
        } else if($newStatus == OrderStates::SHIPPED) {
            $ordersDAO->update($orderID, array("shippingDate" => date('Y-m-d H:i:s')));
        }
        $ordersDAO->update($orderID, array("status" => $newStatus));
        $result = array("status" => true);
        header('Content-Type: application/json');
        echo json_encode($result);
    } else if($action == "add") {
        $orderData = Array();
        $orderData["additionalInfo"] = htmlspecialchars($_POST["additionalInfo"]);
        $useMyAddress = $_POST["useMyAddress"];
        if($useMyAddress == 'true') {
            $id = $addressesDAO->getAddressID($_SESSION["username"]);
            $orderData["addressID"] = $id;
        } else {
            $validationResult = validateNewAddress($_POST["newAddress"]);
            if ($validationResult["status"] == false) {
                header('Content-Type: application/json');
                echo json_encode($validationResult);
                return;
            }
            $orderData["addressID"] = $addressesDAO->save($_POST["newAddress"]);
        }
        foreach ($_SESSION["products"] as $key => $product) {
            //validate if there is enough of this product
            $productID = $_SESSION["products"][$key]["productID"];
            $requestedQuantity = $_SESSION["products"][$key]["quantity"];
            $validationResult = validateProductAvailability($requestedQuantity, $productID, $productDAO);
            if($validationResult["status"] == false) {
                header('Content-Type: application/json');
                echo json_encode($validationResult);
                return;
            }
            $orderData["username"] = $_SESSION["username"];
            $orderData["status"] = INITIAL_ORDER_STATUS;
            $orderData["orderDate"] = date('Y-m-d H:i:s');
            //TODO should happen after all validations pass
            $orderID = $ordersDAO->save($orderData);
            //persist the new order -> product to the joining table
            $orderProduct = array();
            $orderProduct["productID"] = $productID;
            $orderProduct["orderID"] = $orderID;
            $orderProduct["quantity"] = $requestedQuantity;
            //The snapshot of the current product price is persisted too, because it
            //changes over time and there might be a discount
            $productPrice = $_SESSION["products"][$key]["price"];
            $orderProduct["historicPrice"] = $productPrice;
            $finalID = $ordersProductsDAO->save($orderProduct);
            //TODO maybe this decrementation should happen when the order is shipped?
            $productDAO->decrementQuantity($productID, $requestedQuantity);
            //reset shopping cart in the end
            $_SESSION["products"] = [];
        }
        $result = array("status" => true, "orders-products ID" => $finalID);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}

/**
 * Validates if the product is available and there is enough quantity to match the requested one.
 */
function validateProductAvailability($requestedQuantity, $productID, $productDAO) {
    $validationResult = array();
    //fetch the product title in to construct the error message
    $product = $productDAO->getByID($productID);
    $productTitle = $product["title"];
    $availableQuantity = $productDAO->getQuantityInStock($productID);

    if($product["available"] == 0) {
        $validationResult["status"] = false;
        $validationResult["errorsMapping"] = array();
        $validationResult["errorsMapping"][$productTitle] = "product.not.available";
        return $validationResult;
    }
    if($requestedQuantity > $availableQuantity) {
        $validationResult["status"] = false;
        $validationResult["errorsMapping"] = array();
        $validationResult["errorsMapping"][$productTitle] = "not.enough.quantity.of.product";
        return $validationResult;
    }
    $validationResult["status"] = true;
    return $validationResult;
}

/**
 * Validates the new address.
 *
 * @param $newAddress
 * @return array
 */
function validateNewAddress($newAddress) {
    $email = $newAddress["email"];
    $town = $newAddress["town"];
    $zipCode = $newAddress["zipCode"];
    $address = $newAddress["address"];
    $phone = $newAddress["phone"];
    $isValid = true;
    $validationMessage = "";
    if (empty($email) || !preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $email)) {
        $validationMessage.= "email.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the town using a regex pattern
    if (empty($town) || !preg_match('/^([a-zA-Z\s]){0,50}$/', $town)) {
        $validationMessage.= "town.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the zip code using a regex pattern
    if (empty($zipCode) || !preg_match('/^([a-zA-Z0-9\s\-]){0,12}$/', $zipCode)) {
        $validationMessage.= "zip.code.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the address using a regex pattern
    if (empty($address) || !preg_match('/^([a-zA-Z0-9\s\_\-\,\.]){0,70}$/', $address)) {
        $validationMessage.= "address.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the phone using a regex pattern
    if (empty($phone) || !preg_match('/^([0-9+]){0,20}$/', $phone)) {
        $validationMessage.= "phone.number.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    $response = array();
    $response["status"] = $isValid;
    $response["message"] = $validationMessage;
    return $response;
}

?>