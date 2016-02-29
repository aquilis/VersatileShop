<?php

include_once '../lib/utils.php';

/**
 *  An abstract, parent DAO class. Provides common functionality for interaction 
 *  with the data tier, thus giving another level of abstraction.
 * 
 *  @author Vilizar Tsonev 
 *  @since 27.01.2015
 * 
 * */
abstract class BaseDAO {

    protected $dbConnection;
    protected $dataTable;
    protected $primaryKeyColumnName;

    function __construct($dataTable) {
        $this->dbConnection = getVersatileShopDbConnection();
        $this->dataTable = $dataTable;
        $this->primaryKeyColumnName = $this->getPrimaryKeyColumnName();
    }

    /**
     *  Saves the given data into the database.
     *
     * @param $data - is an associative array, where the key is to column name to insert to, and the value is 
     *                the value to insert.
     * */
    public function save($data) {
        $fields = "(";
        $values = "(";
        $isFirst = true;
        foreach ($data as $key => $value) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $fields.= ",";
                $values.= ",";
            }
            $fields.= mysqli_real_escape_string($this->dbConnection, $key);
            $values.= "'" . mysqli_real_escape_string($this->dbConnection, $value) . "'";
        }
        $fields.= ")";
        $values.= ")";
        $sqlQuery = "INSERT INTO " . $this->dataTable . " " . $fields . " VALUES " . $values . ";";
        mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        //get the auto-generated ID and return it (used to later insert items in the foreign key-connected tables)
        $getGeneratedIdQuery = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($this->dbConnection, $getGeneratedIdQuery) or trigger_error("Query Failed: " . mysql_error());
        $row = mysqli_fetch_assoc($result);
        return $row['LAST_INSERT_ID()'];
    }

    /**
     *  Updates the row with the given ID.
     *
     * @param $id - is the id of the row to update
     * @param $data - is the new data to insert - an associative array, where the key is the column name to insert to, and the value is 
     *                the value to insert.
     * */
    public function update($id, $data) {
        if (!$this->exists($id)) {
            return "A record with that ID does not exist";
        }
        $setters = "SET ";
        $isFirst = true;
        foreach ($data as $key => $value) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $setters.= ",";
            }
            $setters.= mysqli_real_escape_string($this->dbConnection, $key) . "=" .
                    "'" . mysqli_real_escape_string($this->dbConnection, $value) . "'";
        }
        $sqlQuery = "UPDATE " . $this->dataTable . " " . $setters . " WHERE " .
                $this->primaryKeyColumnName . "=" . mysqli_real_escape_string($this->dbConnection, $id) . ";";
        mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
    }

    /**
     *  Deletes the row with the given ID.
     *
     * @param $id - is the ID of the row to delete
     * */
    public function delete($id) {
        $sqlQuery = "DELETE FROM " . $this->dataTable . " WHERE " .
                $this->primaryKeyColumnName . "=" .
                mysqli_real_escape_string($this->dbConnection, $id) . ";";
        mysqli_query($this->dbConnection, $sqlQuery);
        return mysqli_affected_rows($this->dbConnection) . " rows deleted.";
    }

    /**
     *  Gets all fields and all rows from the data table.
     * */
    public function getAll() {
        $query = "SELECT * FROM " . $this->dataTable . ";";
        $result = mysqli_query($this->dbConnection, $query) or trigger_error("Query Failed: " . mysql_error());
        $all = array();
        $rowArray = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($all, $row);
        }
        return $all;
    }

    /**
     *  Gets the data only for the given column names from the DB (a projection).
     *
     *   @param fields - is an array of the column names to retrieve
     * */
    public function getProjectionOf($fields) {
        if (count($fields) == 0) {
            return array();
        }
        $columnNames = $this->buildColumnNamesSubQuery($fields);
        $query = "SELECT " . $columnNames . " FROM " . $this->dataTable . ";";
        $result = mysqli_query($this->dbConnection, $query) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        $rowArray = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }

    /**
     *  Gets the data, accroding to the given criteria, fields projection and order. The criteria matching mechanism is 
     *  by string only, and uses 'contains' (non-exact), case-insensitive matching.
     *  If only retrieval by criteria is needed, see getBySimpleCriteria
     *
     *   @param fields - is an array of column names to retrive projection for (pass an empty array() if not needed)
     *
     *   @param criteria - is an associative array, where the keys are the column names 
     *                     and the values are their expected values to match the criteria. The method uses exact matching (with =) 
     *                     by default, but wildcards can be used for 'contains' match. 
     *                     For example: ['title'=>'*lord of*'] will match all titles that contain "lord of"
     *                     while ['title'=>'lord of'] will match only titles which have the exact content of "lord of"
     *
     *   @param orderByColumn - is the column by which to order the result (pass an empty string if not needed)
     *
     *   @param orderDirection - is the order direction - ASC/DESC (pass an empty string if not needed)
     * */
    public function getByCriteria($fields, $criteria, $orderByColumn, $orderDirection) {
        if (count($criteria) == 0) {
            return array();
        }
        $columnNames = "*";
        if (count($fields) > 0) {
            $columnNames = $this->buildColumnNamesSubQuery($fields);
        }
        $searchQuery = "SELECT " . $columnNames . " FROM " . $this->dataTable . " WHERE (";
        //if the criteria is not empty, start building the query
        $isFirst = true;
        foreach ($criteria as $key => $value) {
            //all search arguments (like title, description, etc) have an 'AND' relation between them (if more than 1)
            if ($isFirst) {
                $isFirst = false;
            } else {
                $searchQuery.= " AND ";
            }
            $key = mysqli_real_escape_string($this->dbConnection, $key);
            $value = mysqli_real_escape_string($this->dbConnection, $value);
            $clause = $key . "='" . $value . "'";
            //parse the wildcards (if any) and turn them into a LIKE clause
            if (($value[0] == "*") && ($value[strlen($value) - 1] == "*")) {
                //get the value without the wildcards
                $valueTrimmedWildcards = substr($value, 1, strlen($value) - 2);
                $clause = $key . " LIKE '%" . $valueTrimmedWildcards . "%'";
            }
            $searchQuery.= $clause;
        }
        $searchQuery.= ")";
        if ((strlen($orderByColumn) > 0) && (strlen($orderDirection) > 0)) {
            $searchQuery.= " order by " . mysqli_real_escape_string($this->dbConnection, $orderByColumn) .
                    " " . mysqli_real_escape_string($this->dbConnection, $orderDirection);
        }
        $result = mysqli_query($this->dbConnection, $searchQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        $rowArray = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }

    /**
     *  Gets the data, accroding to the given criteria.
     *
     *   @param criteria - is an associative array, where the keys are the column names 
     *                     and the values are their expected values to match the criteria. The method uses exact matching (with =) 
     *                     by default, but wildcards can be used for 'contains' match. 
     *                     For example: ['title'=>'*lord of*'] will match all titles that contain "lord of"
     *                     while ['title'=>'lord of'] will match only titles which have the exact content of "lord of"
     * */
    public function getBySimpleCriteria($criteria) {
        return $this->getByCriteria(array(), $criteria, "", "");
    }

    /**
     *  Checks if a record with the given ID exists.
     *
     *  @param $id is the ID
     * */
    public function exists($id) {
        $sqlQuery = mysqli_query($this->dbConnection, "SELECT " . $this->primaryKeyColumnName .
                " FROM " . $this->dataTable . " WHERE " . $this->primaryKeyColumnName . " = '" .
                mysqli_real_escape_string($this->dbConnection, $id) . "' LIMIT 1");
        return (mysqli_num_rows($sqlQuery) > 0);
    }

    /**
     *  Gets the record with the given ID.
     *
     *  @param $id is the ID
     * */
    public function getByID($id) {
        $query = "SELECT * FROM " . $this->dataTable . "
                 WHERE " . $this->primaryKeyColumnName . " = '" . mysqli_real_escape_string($this->dbConnection, $id) . "' LIMIT 1;";
        $result = mysqli_query($this->dbConnection, $query) or trigger_error("Query Failed: " . mysql_error());
        //wrap ths student data inside an associative array
        $data = mysqli_fetch_assoc($result);
        return $data;
    }

    /*     * **** PRIVATE METHODS ***** */

    /**
     *  Builds a subquery string containing the column names, taken from the given array, separated by commas.
     *
     *  @param $fields - is an array of column names
     * */
    private function buildColumnNamesSubQuery($fields) {
        if (count($fields) == 0) {
            return "";
        }
        $columnNames = "";
        foreach ($fields as $field) {
            if (strlen($columnNames) > 0) {
                $columnNames.=",";
            }
            $columnNames.= mysqli_real_escape_string($this->dbConnection, $field);
        }
        return $columnNames;
    }

    /**
     *   Gets the column name of the primary key for the associated data table.
     * */
    private function getPrimaryKeyColumnName() {
        $row = mysqli_fetch_assoc(mysqli_query($this->dbConnection, "SHOW KEYS FROM " . $this->dataTable . " WHERE Key_name = 'PRIMARY'"));
        $columnName = $row['Column_name'];
        return $columnName;
    }

}

?>