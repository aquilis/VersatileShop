<?php
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
?>
