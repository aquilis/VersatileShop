<?php

include '../lib/utils.php';

//get the database connection
$dbConnection = getVersatileShopDbConnection();

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "POST") {
    //The username and password are deliberately passed without any escaping or filtering
    //in order for the validation to detect them.
    $validationResult = validateRegistration($_POST["username"], $_POST["password"], $_POST["email"], $dbConnection);
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
    $queryResult = createAccount($username, $password, $email, $dbConnection);
    header('Content-Type: application/json');
    echo json_encode($queryResult);
}

/**
 * Validates the length, content of the username/password and the uniqueness of the username in
 * the database.
 * 
 * @param type $username is the username
 * @param type $pass is the password
 * @param type $dbConnection is the database connection
 * @return a response object with a boolean status field and a messages field
 */
function validateRegistration($username, $pass, $email, $dbConnection) {
    $isValid = true;
    $validationMessage = "";
    //validate the username using a regex pattern
    if (!preg_match('/^[a-zA-Z0-9]{5,15}$/', $username)) {
        $validationMessage .= "Username must contain only latin letters or numbers, no spaces and be between 5 and 15 characters long.".PHP_EOL;
        $isValid = false;
    }
    //validate the password using a regex pattern
    if (!preg_match('/^[a-zA-Z0-9]{6,20}$/', $pass)) {
        $validationMessage .= "Password must contain only latin letters or numbers, no spaces and be between 6 and 20 characters long.".PHP_EOL;
        $isValid = false;
    }
    //validate the email using a regex pattern
    if (!preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $email)) {
        $validationMessage.= "Invalid e-mail format.".PHP_EOL;
        $isValid = false;
    }
    //check if the username already exists in the DB, if it's valid so far
    if ($isValid) {
        $usernameQuery = mysqli_query($dbConnection, "SELECT username FROM users WHERE username = '" .
                mysqli_real_escape_string($dbConnection, $username) . "' LIMIT 1");
        if (mysqli_num_rows($usernameQuery) > 0) {
            $validationMessage .= "That username is already taken by another user.";
            $isValid = false;
        }
        $emailQuery = mysqli_query($dbConnection, "SELECT email FROM users WHERE email = '" .
                mysqli_real_escape_string($dbConnection, $email) . "' LIMIT 1");
        if (mysqli_num_rows($emailQuery) > 0) {
            $validationMessage .= "That e-mail is already in use by another user.";
            $isValid = false;
        }
    }
    $response = array();
    $response["status"] = $isValid;
    $response["message"] = $validationMessage;
    return $response;
}

/**
 * Creates a new account in the DB with the given username and password, supposing they are already validated.
 * 
 * @param $username is the username
 * @param $pass is the password
 * @param $email is the email
 * @param type $dbConnection is the database connection
 * @return a response object with a boolean status field and a messages field
 */
function createAccount($username, $pass, $email, $dbConnection) {
    if (isLogged()) {
        logOut();
    }
    $query = "INSERT INTO users (username, password, email)
            VALUES ('" . mysqli_real_escape_string($dbConnection, $username) .
            "', '" . hashPassword($pass, SALT1, SALT2) ."', '". mysqli_real_escape_string($dbConnection, $email)."')";
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

?>