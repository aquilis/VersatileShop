<?php	

				if($_SERVER["REQUEST_METHOD"] == "GET") {
					//returns all available titles in the shop (used usually for listing or autocomplete)
					if((isset($_GET["action"])) && ($_GET["action"]=="allTitles")) {
						$con = mysqli_connect("localhost","root","","db_versatile_shop"); 
						if (mysqli_connect_errno()) {
							echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}
						//get the titles of all available products in the db
						$query = "SELECT title from products where available = true";    
						$result = mysqli_query($con, $query);
						$jsonData = array();
						while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
	    					array_push($jsonData, $row[0]);
	    				}
						header('Content-Type: application/json');
						echo json_encode($jsonData);
					} else if ($_GET["action"]=="search") {
						//initialize the mysql connection
						$con = mysqli_connect("localhost","root","","db_versatile_shop"); 
						if (mysqli_connect_errno()) {
							echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}
						//all search args will be stored inside the criteria array
						$criteria = array();
						$isFirst = true;
						//build the SQL search query, retrieving only the basic product details for listing purpose. The final form of the query is something like:
						//SELECT productID, title, SUBSTRING(description,1,220), price, thumbnailPath FROM products WHERE available=true AND (title LIKE '%gta%') order by dateAdded desc
						$searchQuery = "SELECT productID, title, SUBSTRING(description,1,220), price, thumbnailPath FROM products WHERE available=true AND (";
						if (isset($_GET["title"])) {
							$criteria["title"] = $_GET["title"];
						}
						if (isset($_GET["description"])) {
							$criteria["description"] = $_GET["description"];
						}
						//if the criteria array is empty, just return an empty json array and terminate the function
						if(count($criteria)==0) {
							header('Content-Type: application/json');
							echo json_encode(array());
							return;
						}
						//if the criteria is not empty, start building the query
						foreach ($criteria as $key => $value) {
							//all search arguments have an 'OR' relation between them (if more than 1)
							if($isFirst) {
								$isFirst = false;
							} else {
								$searchQuery.= " OR ";	
							}
							$searchQuery.= $key ." LIKE '%".mysqli_real_escape_string($con, $value)."%'";
						}
						$searchQuery.= ") order by dateAdded desc";
						$result = mysqli_query($con, $searchQuery);
						$jsonData = array();
						$rowArray = array();
						while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
							$rowArray['productID'] = $row[0];
	   						$rowArray['title'] = $row[1];
	    					$rowArray['description'] = $row[2];
	    					$rowArray['price'] = $row[3];
	    					$rowArray['thumbnailPath'] = $row[4];
	    					array_push($jsonData, $rowArray);
	    				}
						header('Content-Type: application/json');
						echo json_encode($jsonData);
					}	
				} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
					//implement the POST handler here
				}
?>