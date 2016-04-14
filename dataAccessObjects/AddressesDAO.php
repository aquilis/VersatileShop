<?php
include_once 'BaseDAO.php';
/**
 *  A data access object for the addresses data table.
 *
 *  @author Vilizar Tsonev
 *
 * */
class AddressesDAO extends BaseDAO {

    /**
     * Gets the address ID for the concrete username.
     *
     * @param $username is the username
     * @return array|null
     */
    public function getAddressID($username) {
        $query = "SELECT addresses.addressID FROM " . $this->dataTable . " , users WHERE " . $this->dataTable . "." .  $this->primaryKeyColumnName .
            " = users." . $this->primaryKeyColumnName . " AND users.username = '" . mysqli_real_escape_string($this->dbConnection, $username) .  "' LIMIT 1;";
        $result = mysqli_query($this->dbConnection, $query) or trigger_error("Query Failed: " . mysql_error());
        $data = mysqli_fetch_assoc($result);
        return $data[$this->primaryKeyColumnName];
    }

}