<?php	

				if($_SERVER["REQUEST_METHOD"] == "GET") {
					if(isset($_GET["productID"])) {
						$con = mysqli_connect("localhost","root","","db_versatile_shop"); 
						if (mysqli_connect_errno()) {
							echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}
						//get the basic product details
						$query = "SELECT  productID, title, description, price, manufacturer, dateAdded FROM products WHERE productID=".mysqli_real_escape_string($con, $_GET["productID"]." LIMIT 1");    
						$result = mysqli_query($con, $query);
						$row = mysqli_fetch_array($result, MYSQL_NUM);
						$jsonData = array();
						$jsonData['productID'] = $row[0];
						$jsonData['title'] = $row[1];
						$jsonData['description'] = $row[2];
						$jsonData['price'] = $row[3];
						$jsonData['manufacturer'] = $row[4];
						$jsonData['dateAdded'] = $row[5];
						//get the images for this product from the images table and add them to the response
						$query = "SELECT imagePath, imageDescription FROM images inner join products on images.productID=products.productID where products.productID=".mysqli_real_escape_string($con, $_GET["productID"]);    
						$result = mysqli_query($con, $query);
						$jsonData['images'] = array();
						$rowArray = array();
						while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
							$rowArray['imagePath'] = $row[0];
							$rowArray['imageDescription'] = $row[1];
	    					array_push($jsonData['images'], $rowArray);
	    				}
	    				//get the videos for this product from the videos table and add them to the response
						$query = "SELECT videoSrc, videoCaption FROM videos inner join products on videos.productID=products.productID where products.productID=".mysqli_real_escape_string($con, $_GET["productID"]);    
						$result = mysqli_query($con, $query);
						$jsonData['videos'] = array();
						$rowArray = array();
						while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
							$rowArray['videoSrc'] = $row[0];
							$rowArray['videoCaption'] = $row[1];
	    					array_push($jsonData['videos'], $rowArray);
	    				}
						header('Content-Type: application/json');
						echo json_encode($jsonData);
					} else {
						//return all products (basic details only) for listing purpose
						$con = mysqli_connect("localhost","root","","db_versatile_shop"); 
						if (mysqli_connect_errno()) {
							echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}
						$query = "SELECT productID, title, SUBSTRING(description,1,220), price, thumbnailPath FROM products WHERE available=true order by dateAdded desc";    
						$result = mysqli_query($con, $query);

						$jsonData = array();
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