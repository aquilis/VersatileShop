<?php
/**
 * A moduile containing common utility functions used all across the system
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Gets the connection to the Versatile shop's database.
 * 
 * @return the connection object to the db_versatile_shop
 */
//TODO: move the host name, database name, user/pass in a config file
function getVersatileShopDbConnection() {
    //opens a persistent connection to the database, to prevent reconnecting each time
    //this method is invoked
    $con = mysqli_connect("p:localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    // Change character set to utf8
    mysqli_set_charset($con,"utf8");
    return $con;
}

/**
 * Retunrns an empty json array to the client-side.
 */
function returnEmptyJsonArray() {
    header('Content-Type: application/json');
    echo json_encode(array());
}

function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

/*
  password salts are used to ensure a secure password
  hash and make your passwords much harder to be broken into
  Change these to be whatever you want, just try and limit them to
  10-20 characters each to avoid collisions.
*/
define('SALT1', '24859f@#$#@$');
define('SALT2', '^&@#_-=+Afda$#%');

/**
 * Escapes all shell arguments and Html tags from the string.
 * */
function escapeShellAndHtml($rawString) {
    return htmlspecialchars($rawString, ENT_QUOTES);
}

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
 * Checks if the given username is an admin.
 * */
function isAdmin($username) {
    $con = getVersatileShopDbConnection();
    $sql = "SELECT userID FROM admins WHERE userID = (SELECT userID from users where username = '" .
            mysqli_real_escape_string($con, $username) . "') LIMIT 1";
    $query = mysqli_query($con, $sql) or trigger_error("Query Failed: " . mysql_error());
    return mysqli_num_rows($query) == 1;
}
?>
