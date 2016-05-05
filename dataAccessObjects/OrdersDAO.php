<?php
include_once 'BaseDAO.php';
include_once '../services/OrderStates.php';

/**
 *  A data access object for the orders data table.
 * 
 *  @author Vilizar Tsonev
 * 
 * */
class OrdersDAO extends BaseDAO {

    const ACTIVE = "active";
    const ENDED = "ended";
    const ALL = "all";

    function __construct($dataTable) {
        parent::__construct($dataTable);
    }

    /**
     * Gets all active orders of  the given user name.
     */
    public function getActiveOrdersFor($userName) {
        return $this->getOrdersInternal($userName, $this::ACTIVE);
    }

    /**
     * Gets all ended orders of  the given user name.
     */
    public function getEndedOrdersFor($userName) {
        return $this->getOrdersInternal($userName, $this::ENDED);
    }

    /**
     * Gets all orders of  the given user name, no matter what their states are.
     */
    public function getAllOrdersFor($userName) {
        return $this->getOrdersInternal($userName, $this::ALL);
    }

    /**
     * Gets all orders for the provided user name, according to the allowed states param passed to the method.
     *
     * @param $userName is the user name
     * @param $allowedStates is a string constant indicating which status set should be retrieved
     * @return the array of orders
     */
	private function getOrdersInternal($userName, $allowedStates) {
        $statusClause = "";
        if ($allowedStates == $this::ACTIVE) {
            $statusClause = " AND orders.status NOT IN ('" . OrderStates::RECEIVED . "', '" . OrderStates::REJECTED . "') ";
        } else if ($allowedStates == $this::ENDED) {
            $statusClause = " AND orders.status IN ('" . OrderStates::RECEIVED . "', '" . OrderStates::REJECTED . "') ";
        }
        $sqlQuery = "SELECT orders.orderDate, orders.shippingDate, orders.status, users.username,
							products.productID, products.title, products.thumbnailPath, products.manufacturer,
							orders_products.quantity, orders_products.historicPrice
					FROM orders, users, orders_products, products WHERE
					orders.username = users.username AND
                    orders.orderID= orders_products.orderID AND
                    orders_products.productID = products.productID AND
                    users.username = '". mysqli_real_escape_string($this->dbConnection, $userName) . "'" .
                    $statusClause .
                    " ORDER BY orders.orderDate DESC";
        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }
}
?>