<?php
include_once '../lib/utils.php';
include_once 'BaseDAO.php';
include_once 'AddressesDAO.php';

/**
 *  A data access object for the users data table.
 *
 *  @author Vilizar Tsonev
 *
 * */
class UsersDAO extends BaseDAO {

    private $addressesDAO;

    function __construct($dataTable) {
        parent::__construct($dataTable);
        $this->addressesDAO = new AdressesDAO('addresses');
    }

    /**
     *  Registers the validated user data.
     * @param $userData is the user data
     */
    public function registerValidatedUser($userData) {
        $addressID = $this->persistUserAddress($userData);
        $this->persistUserData($userData, $addressID);
    }

    /**
     * 	Checks if the given username is an admin.
     *  <b>Important:</b> Always first make sure that the username is authenticated, before calling this method.
     * */
    public function isAdminUser($username) {
        $sql = "select username from admins where username= '" . mysqli_real_escape_string($this->dbConnection, $username) . "' limit 1 ";
        $result = mysqli_query($this->dbConnection, $sql) or trigger_error("Query Failed: " . mysql_error());
        return mysqli_num_rows($result) > 0;
    }

    public function userExists($username, $password) {
        $criteria = array("username" => $username, "password" => $password);
        $result = $this->getBySimpleCriteria($criteria);
        return count($result) > 0;
    }

    private function persistUserAddress($userData) {
        $town = $userData["town"];
        $zipCode = $userData["zipCode"];
        $address = $userData["address"];
        $email = $userData["email"];
        $phone = $userData["phone"];
        $address = array("town" => $town, "zipCode" => $zipCode, "email" => $email, "address" => $address, "phone" => $phone);
        return $this->addressesDAO->save($address);
    }

    private function persistUserData($userData, $addressID) {
        $username = $userData["username"];
        $pass = $userData["password"];
        $firstName = $userData["firstName"];
        $lastName = $userData["lastName"];
        $user = array("username" => $username, "firstName" => $firstName, "lastName" => $lastName,
            "password"=> hashPassword($pass, SALT1, SALT2), "addressID" => $addressID);
        $this->save($user);
    }

    /**
     * Builds the SQL query for inserting the given validated data for the new user into the database.
     *
     * @param $userData is an associative array containing all user data
     * @param type $dbConnection is the database connection
     * @return the SQL query
     */
    private function buildCreateUserSqlQuery($userData) {
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
        $values = "('" . mysqli_real_escape_string($this->dbConnection, $username) . "', '" .
            hashPassword($pass, SALT1, SALT2) . "', '" .
            mysqli_real_escape_string($this->dbConnection, $email) . "'";

        //check if any of the additional details is set and append it to the query
        //NOTE: iterating over the $userData and putting its key => values directly into the query has vulnerabilites and is avoided
        if (isset($firstName)) {
            $fields.= ", firstName";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $firstName) . "'";
        }
        if (isset($lastName)) {
            $fields.= ", lastName";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $lastName) . "'";
        }
        if (isset($town)) {
            $fields.= ", town";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $town) . "'";
        }
        if (isset($zipCode)) {
            $fields.= ", zipCode";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $zipCode) . "'";
        }
        if (isset($address)) {
            $fields.= ", address";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $address) . "'";
        }
        if (isset($phone)) {
            $fields.= ", phone";
            $values.= ", '" . mysqli_real_escape_string($this->dbConnection, $phone) . "'";
        }
        $fields .= ")";
        $values.= ")";
        $query = "INSERT INTO users " . $fields . " VALUES " . $values . ";";
        return $query;
    }
}
?>