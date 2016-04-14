<?php
include '../lib/utils.php';
include "../dataAccessObjects/OrdersDAO.php";
include "../dataAccessObjects/AddressesDAO.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$ordersDAO = new OrdersDAO("orders");

$addressesDAO = new AddressesDAO("addresses");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    if (isset($_GET["username"])) {
        $jsonData = $ordersDAO->getOrdersHistoryForUsername($username);
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
} else if ($requestMethod == "POST") {
    $orderData = Array();
    $orderData["additionalInfo"] = $_POST["additionalInfo"];
    $useMyAddress = $_POST["useMyAddress"];
    if($useMyAddress == 'true') {
        $id = $addressesDAO->getAddressID($_SESSION["username"]);
        $orderData["addressID"] = $id;
    } else {
        $validationResult = validateNewAddress($_POST["newAddress"]);
        if ($validationResult["status"] == true) {
            //once validated, save the new address into the DB
            $orderData["addressID"] = $addressesDAO->save($_POST["newAddress"]);
        } else {
            header('Content-Type: application/json');
            echo json_encode($validationResult);
            return;
        }
    }
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