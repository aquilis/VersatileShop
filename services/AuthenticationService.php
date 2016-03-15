<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once '../lib/utils.php';
include_once "../dataAccessObjects/UsersDAO.php";

//get the database connection
$dbConnection = getVersatileShopDbConnection();

$usersDAO = new UsersDAO("users");

//avoid special characters and sql injection
$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);

if ($requestMethod == "GET") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if($action == "logout") {
        logOut();
        $response = array();
        $response["status"] = true;
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
} else if ($requestMethod == "POST") {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    if($action == "authenticate") {
        $username = htmlspecialchars($_POST["username"], ENT_QUOTES);
        $password= htmlspecialchars($_POST["password"], ENT_QUOTES);
        $success = authenticate($username, $password, $usersDAO);
        $response = array();
        if ($success) {
            $response["status"] = true;
            $response["message"] = "login.successful ";
        } else {
            $response["status"] = false;
            $response["message"] = "login.invalid.message";
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
}

/**
 * If a user with that credential exists, authenticates it and records its data into the session.
 * Otherwise, returns false.
 *
 * @param $username is the username
 * @param $password is the password
 * @param $usersDAO is the users dao
 * @return boolean indicating if the user has been successfully authenticated
 */
function authenticate($username, $password, $usersDAO) {
    $hashedPassword = hashPassword($password, SALT1, SALT2);
    $exists = $usersDAO->userExists($username, $hashedPassword);
    if($exists) {
        $_SESSION['isLogged'] = 1;
        $_SESSION['username'] = $username;
        if($usersDAO->isAdminUser($username)) {
            $_SESSION['isAdmin'] = 1;
        }
    } else {
        $_SESSION['isLogged'] = 0;
    }
    return $exists;
}

/**
 * Logs out the currently logged user.
 * */
function logOut() {
    if ( $_SESSION['isLogged'] == 1) {
        unset($_SESSION['isLogged']);
        if (isset($_SESSION['isAdmin'])) {
            unset($_SESSION['isAdmin']);
        }
        if (isset($_SESSION['products'])) {
            unset($_SESSION['products']);
        }
    }
}

?>