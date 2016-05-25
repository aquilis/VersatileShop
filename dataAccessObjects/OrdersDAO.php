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

    const DAY = "day";
    const MONTH = "month";
    const YEAR = "year";


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
     * Gets the orders according to the provided criteria.
     *
     * @param $criteria is a map containing the filter field=>values. It may contain the following keys:
     *                  username, dateFrom and status which have to be mapped to their expected values.
     * @return array of the retrieved results.
     */
    public function getOrdersByCriteria($criteria) {
        $usernameCondition = "";
        if(isset($criteria["username"])) {
            $usernameCondition = " AND users.username = '". mysqli_real_escape_string($this->dbConnection, $criteria["username"]) . "' ";
        }
        $dateFromCondition = "";
        if(isset($criteria["dateFrom"])) {
            $time = strtotime($criteria["dateFrom"]);
            $dateFrom = date('Y-m-d', $time);
            $dateFromCondition = " AND orders.orderDate > '" . $dateFrom . "' ";
        }
        $statusCondition = "";
        if(isset($criteria["status"])) {
            $statusCondition = " AND orders.status ='" . mysqli_real_escape_string($this->dbConnection, $criteria["status"]) . "' ";
        }
        $sqlQuery = "SELECT orders.orderDate, orders.shippingDate, orders.status, users.username,
							products.productID, products.title, products.thumbnailPath, products.manufacturer,
							orders_products.quantity, orders_products.historicPrice
					FROM orders, users, orders_products, products WHERE
					orders.username = users.username AND
                    orders.orderID= orders_products.orderID AND
                    orders_products.productID = products.productID" .
                    $usernameCondition .
                    $dateFromCondition .
                        $statusCondition .
                    " ORDER BY orders.orderDate DESC";
        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
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

    /**
     * Gets the count of supplies for each product.
     *
     * @return array with the retrieved data
     */
    public function getProductsBySupply() {
        $sqlQuery = "select products.productID, products.title, COUNT(supplies.supplyID) as suppliesCount
                    from supplies, products WHERE supplies.productID=products.productID GROUP by products.productID";
        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }

    /**
     * Gets the revenue aggregated by time.
     *
     * @param $timePeriod is the time period
     * @return associative array with the aggregated data
     */
    public function getRevenueByTime($timePeriod) {
        $aggregateAttribute = "orders.orderDate";
        $groupBy = "orderDate";
        if(isset($timePeriod) && $timePeriod == $this::MONTH) {
            $aggregateAttribute = "month(orders.orderDate)";
            $groupBy = "month(orderDate)";
        } else if (isset($timePeriod) && $timePeriod == $this::YEAR) {
            $aggregateAttribute = "year(orders.orderDate)";
            $groupBy = "year(orderDate)";
        }
        $sqlQuery = "SELECT ". $aggregateAttribute . " , (COUNT(orders.orderID) * orders_products.quantity * orders_products.historicPrice)
                        AS revenue FROM orders inner join orders_products
                        on orders.orderID=orders_products.orderID
                        group by " . $groupBy .";";

        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }

    /**
     * Gets the market share by product.
     *
     * @param $timePeriod is the time period
     * @return array is the associative array
     */
    public function getMostBoughtProduct() {
        $sqlQuery = "SELECT products.productID, products.title, COUNT(orders.orderID) AS productOrdersCount
                    FROM products
                    inner join orders_products on products.productID=orders_products.productID
                    inner JOIN orders on orders.orderID=orders_products.orderID
                    group by productID";
        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }

    /**
     * Gets all orders aggregated by the given time period.
     *
     * @param $timePeriod is the time period to group by
     * @return an associative array with all aggregated orders by time
     */
    public function getOrdersGroupedByTime($timePeriod) {
        $aggregateAttribute = "orders.orderDate";
        $groupBy = "orderDate";
        if(isset($timePeriod) && $timePeriod == $this::MONTH) {
            $aggregateAttribute = "month(orders.orderDate)";
            $groupBy = "month(orderDate)";
        } else if (isset($timePeriod) && $timePeriod == $this::YEAR) {
            $aggregateAttribute = "year(orders.orderDate)";
            $groupBy = "year(orderDate)";
        }
        $sqlQuery = "SELECT ". $aggregateAttribute . " , COUNT(orders.orderID) AS ordersCount
            FROM orders
            inner join orders_products
            on orders.orderID=orders_products.orderID
            group by " . $groupBy .";";
        $result = mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
    }
}
?>