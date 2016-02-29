<?php
include_once 'BaseDAO.php';

/**
 *  A data access object for the videos data table.
 * 
 *  @author Vilizar Tsonev
 * 
 * */
class OrdersDAO extends BaseDAO {

	public function getOrdersHistoryForUsername($username) {
		$query = "SELECT orders.orderID, orders.orderDate, users.username,
							products.title, orders_products.quantity
					FROM orders
					INNER JOIN users
					ON orders.userID= users.userID
					INNER JOIN orders_products
					ON orders.orderID= orders_products.orderID
					INNER JOIN products
					ON orders_products.productID = products.productID
					WHERE users.username = '". mysqli_real_escape_string($this->dbConnection, $username) ."' ORDER BY orders.orderID";

		//echo "QUERY IS: " . $query;
		$result = mysqli_query($this->dbConnection, $query) or trigger_error("Query Failed: " . mysql_error());
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
        return $data;
	}
}
?>