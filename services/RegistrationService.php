<?php

include '../lib/utils.php';

//get the database connection
$dbConnection = getVersatileShopDbConnection();

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

//messages keyset from the resource bundle


if ($requestMethod == "POST") {
    $userData = Array();
    $userData["username"] = $_POST["username"];
    $userData["password"] = $_POST["password"];
    $userData["email"] = $_POST["email"];
    $userData["firstName"] = $_POST["firstName"];
    $userData["lastName"] = $_POST["lastName"];
    $userData["town"] = $_POST["town"];
    $userData["zipCode"] = $_POST["zipCode"];
    $userData["address"] = $_POST["address"];
    $userData["phone"] = $_POST["phone"];
    //The user data is deliberately passed without any escaping or filtering
    //in order for the validation to detect them.
    $validationResult = validateRegistration($userData, $dbConnection);
    //if the validation has failed, return the status and error and terminate the function.
    if ($validationResult["status"] == false) {
        header('Content-Type: application/json');
        echo json_encode($validationResult);
        return;
    }
    //escaping is not rly needed here, but just in case..
    $username = htmlspecialchars($_POST["username"], ENT_QUOTES);
    $password = htmlspecialchars($_POST["password"], ENT_QUOTES);
    $email = htmlspecialchars($_POST["email"], ENT_QUOTES);
    //create the new account and return the response
    $queryResult = createAccount($userData, $dbConnection);
    header('Content-Type: application/json');
    echo json_encode($queryResult);
}

/**
 * Validates the length, content of the username/password and the uniqueness of the username in
 * the database.
 * 
 * @param $userData is an associative array containing all user data
 * @param $dbConnection is the database connection
 * @return a response object with a boolean status field and a messages field
 */
function validateRegistration($userData, $dbConnection) {
    $username = $userData["username"];
    $pass = $userData["password"];
    $email = $userData["email"];
    $firstName = $userData["firstName"];
    $lastName = $userData["lastName"];
    $town = $userData["town"];
    $zipCode = $userData["zipCode"];
    $address = $userData["address"];
    $phone = $userData["phone"];
    $isValid = true;
    $validationMessage = "";
    //validate the username using a regex pattern
    if (!preg_match('/^[a-zA-Z0-9]{5,15}$/', $username)) {
        $validationMessage .= "username.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the password using a regex pattern
    if (!preg_match('/^[a-zA-Z0-9]{6,20}$/', $pass)) {
        $validationMessage .= "password.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the email using a regex pattern
    if (!preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $email)) {
        $validationMessage.= "email.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //check if the username already exists in the DB, if it's valid so far
    if ($isValid) {
        $usernameQuery = mysqli_query($dbConnection, "SELECT username FROM users WHERE username = '" .
                mysqli_real_escape_string($dbConnection, $username) . "' LIMIT 1");
        if (mysqli_num_rows($usernameQuery) > 0) {
            $validationMessage .= "username.already.used" . PHP_EOL;
            $isValid = false;
        }
        $emailQuery = mysqli_query($dbConnection, "SELECT email FROM users WHERE email = '" .
                mysqli_real_escape_string($dbConnection, $email) . "' LIMIT 1");
        if (mysqli_num_rows($emailQuery) > 0) {
            $validationMessage .= "email.already.used" . PHP_EOL;
            $isValid = false;
        }
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
    //validate the first name using a regex pattern
    if ((isset($firstName)) && (!preg_match('/^([a-zA-Z\s]){0,50}$/', $firstName))) {
        $validationMessage.= "first.name.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the last name using a regex pattern
    if ((isset($lastName)) && (!preg_match('/^([a-zA-Z\s]){0,50}$/', $lastName))) {
        $validationMessage.= "last.name.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the town using a regex pattern
    if ((isset($town)) && (!preg_match('/^([a-zA-Z\s]){0,50}$/', $town))) {
        $validationMessage.= "town.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the zip code using a regex pattern
    if ((isset($zipCode)) && (!preg_match('/^([a-zA-Z0-9\s\-]){0,12}$/', $zipCode))) {
        $validationMessage.= "zip.code.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the address using a regex pattern
    if ((isset($address)) && (!preg_match('/^([a-zA-Z0-9\s\_\-\,\.]){0,70}$/', $address))) {
        $validationMessage.= "address.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    //validate the phone using a regex pattern
    if ((isset($phone)) && (!preg_match('/^([0-9+]){0,20}$/', $phone))) {
        $validationMessage.= "phone.number.registration.fail" . PHP_EOL;
        $isValid = false;
    }
    $response = array();
    $response["status"] = $isValid;
    $response["message"] = $validationMessage;
    $response["errorInMandatoryFields"] = false;
    return $response;
}

/**
 * Creates a new account in the DB with the given username and password, supposing they are already validated.
 * 
 * @param $userData is an associative array containing all user data
 * @param $dbConnection is the database connection
 * @return a response object with a boolean status field and a messages field
 */
function createAccount($userData, $dbConnection) {
    if (isLogged()) {
        logOut();
    }
    $query = buildCreateUserSqlQuery($userData, $dbConnection);
    $queryResult = mysqli_query($dbConnection, $query);
    $result = array();
    if ($queryResult) {
        $result["status"] = true;
        $result["message"] = "Registration was successful.";
    } else {
        $result["status"] = false;
        $result["message"] = "Invalid query: " . mysql_error($dbConnection);
    }
    return $result;
}

/**
 * Builds the SQL query for inserting the given validated data for the new user into the database.
 * 
 * @param $userData is an associative array containing all user data
 * @param type $dbConnection is the database connection
 * @return the SQL query
 */
function buildCreateUserSqlQuery($userData, $dbConnection) {
    $username = $userData["username"];
    $pass = $userData["password"];
    $email = $userData["email"];
    $firstName = $userData["firstName"];
    $lastName = $userData["lastName"];
    $town = $userData["town"];
    $zipCode = $userData["zipCode"];
    $address = $userData["address"];
    $phone = $userData["phone"];

    //first put the mandatory details into the query
    $fields = "(username, password, email";
    $values = "('" . mysqli_real_escape_string($dbConnection, $username) . "', '" .
            hashPassword($pass, SALT1, SALT2) . "', '" .
            mysqli_real_escape_string($dbConnection, $email) . "'";

    //check if any of the additional details is set and append it to the query
    //NOTE: iterating over the $userData and putting its key => values directly into the query has vulnerabilites and is avoided
    if (isset($firstName)) {
        $fields.= ", firstName";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $firstName) . "'";
    }
    if (isset($lastName)) {
        $fields.= ", lastName";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $lastName) . "'";
    }
    if (isset($town)) {
        $fields.= ", town";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $town) . "'";
    }
    if (isset($zipCode)) {
        $fields.= ", zipCode";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $zipCode) . "'";
    }
    if (isset($address)) {
        $fields.= ", address";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $address) . "'";
    }
    if (isset($phone)) {
        $fields.= ", phone";
        $values.= ", '" . mysqli_real_escape_string($dbConnection, $phone) . "'";
    }
    $fields .= ")";
    $values.= ")";
    $query = "INSERT INTO users " . $fields . " VALUES " . $values . ";";
    return $query;
}

?>