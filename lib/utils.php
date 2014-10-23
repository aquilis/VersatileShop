
<?php
/**
 * A moduile containing common utility functions used all across the system
 */


/**
 * Gets the connection to the Versatile shop's database.
 * 
 * @return the connection object to the db_versatile_shop
 */
function getVersatileShopDbConnection() {
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    return $con;
}

/**
 * Retunrns an empty json array to the client-side.
 */
function returnEmptyJsonArray() {
    header('Content-Type: application/json');
    echo json_encode(array());
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
 * Checks if the given username is an admin.
 * */
function isAdmin($username) {
    $con = mysqli_connect("localhost", "root", "", "db_versatile_shop");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $sql = "SELECT userID FROM admins WHERE userID = (SELECT userID from users where username = '" . mysqli_real_escape_string($con, $username) . "') LIMIT 1";
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
 * Validates that the user exists in the database.
 * TODO: Move this to the login model, when ready
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
?>
