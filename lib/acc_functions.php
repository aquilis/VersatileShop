<?php

//start session, if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* * ************* 
  password salts are used to ensure a secure password
  hash and make your passwords much harder to be broken into
  Change these to be whatever you want, just try and limit them to
  10-20 characters each to avoid collisions.
 * ************** */
define('SALT1', '24859f@#$#@$');
define('SALT2', '^&@#_-=+Afda$#%');

/**
 * 	Escapes all shell arguments and Html tags from the string. Use always when storing user inputs into the DB!!!
 * */
// function escapeShellAndHtml($rawString) {
//     return htmlspecialchars($rawString, ENT_QUOTES);
// }

/* * *
  Creates a SHA1 and MD5 hash of the password
  using 2 salts that the user specifies.
 * */

function hashPassword($pPassword, $pSalt1 = "2345#$%@3e", $pSalt2 = "taesa%#@2%^#") {
    return sha1(md5($pSalt2 . $pPassword . $pSalt1));
}

/**
 * Checks if the current session has any login details (the user is logged)
 * */
function isLogged() {
    if ((isset($_SESSION['isLogged'])) && ($_SESSION['isLogged'] == 1)) {
        return true;
    }
    return false;
}

/**
 * Logs out the currently logged user.
 * */
function logOut() {
    if (isLogged()) {
        unset($_SESSION['isLogged']);
        if (isset($_SESSION['isAdmin'])) {
            unset($_SESSION['isAdmin']);
        }
        if (isset($_SESSION['products'])) {
            unset($_SESSION['products']);
        }
        //TODO is sthis cookie needed?
        setcookie('lastLogout', date("d/m/y H:i:s"), 60 * 60 * 24 * 60 + time());
    }
}

/**
 * 	Validates that the user exists in the database.
 * */
function validateLogin($username, $pass) {
    //connect to the 'login' DB
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    // See if the username and password are valid. 
    $sql = "SELECT username FROM users  
			WHERE username = '" . mysqli_real_escape_string($con, $username) . "' AND password = '" . hashPassword($pass, SALT1, SALT2) . "' LIMIT 1";
    $query = mysqli_query($con, $sql) or trigger_error("Query Failed: " . mysql_error());
    if (mysqli_num_rows($query) == 1) {
        mysqli_close($con);
        return true;
    } else {
        mysqli_close($con);
        return false;
    }
}

/**
 * 	Validates the length of the username and password and checks if the username is unique in the Db.
 * */
//TODO obsolete function
function validateRegistration($username, $pass) {
    $username = trim($username);
    $pass = trim($pass);
    $nameLength = strlen($username);
    $passLength = strlen($pass);
    if ($nameLength < 4 || $nameLength > 15) {
        $_SESSION['failedRegMessage'] = "username must be between 4 and 15 characters.<br>";
        return false;
    }
    if ($passLength < 6 || $passLength > 20) {
        $_SESSION['failedRegMessage'] = "password must be between 6 and 20 characters.<br>";
        return false;
    }
    if ($username != escapeShellAndHtml($username)) {
        $_SESSION['failedRegMessage'] = "HTML tags and < > not allowed<br>";
        return false;
    }
    //connect to the 'login' DB
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    //check if the username already exists
    $query = mysqli_query($con, "SELECT username FROM users WHERE username = '" . mysqli_real_escape_string($con, $username) . "' LIMIT 1");
    if (mysqli_num_rows($query) > 0) {
        $_SESSION['failedRegMessage'] = "The username is already taken by another user.<br>";
        return false;
    }
    return true;
}

/**
 * 	Creates the new user account in the database.
 * */
//TODO this is obsolete.. remove it
function createAccount($username, $pass) {
    $username = trim($username);
    $pass = trim($pass);
    if (isLogged()) {
        logOut();
    }
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    //TODO: store all user data into the DB
    mysqli_query($con, "INSERT INTO users (username, password)
					   VALUES ('" . mysqli_real_escape_string($con, escapeShellAndHtml($username)) . "', '" . hashPassword($pass, SALT1, SALT2) . "')");
    mysqli_close($con);
}

/**
 * 	Checks if the given username is an admin (exists in db_login -> admins)
 * */
function isAdmin($username) {
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    //$sql = "SELECT username FROM admins WHERE username = '".mysqli_real_escape_string($con, $username)."' LIMIT 1";
    //$sql = "SELECT username FROM admins WHERE username = (SELECT username from users where username = '" . mysqli_real_escape_string($con, $username) . "') LIMIT 1";
    $sql = "select users.username from users, admins where users.username = admins.username and users.username = '". mysqli_real_escape_string($con, $username) ."' limit 1";
    $query = mysqli_query($con, $sql) or trigger_error("Query Failed: " . mysql_error());
    if (mysqli_num_rows($query) > 0) {
        mysqli_close($con);
        return true;
    } else {
        mysqli_close($con);
        return false;
    }
}

?>
