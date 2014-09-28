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
					}
					
				} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
					//implement the POST handler here
				}
?>