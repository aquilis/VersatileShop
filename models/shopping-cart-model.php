<?php	
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
				//The get mehod is used to get all showcase entries in JSON format
				if($_SERVER["REQUEST_METHOD"] == "GET") {
					if(isset($_SESSION["products"])) {
						    $jsonData = array();
						    foreach ($_SESSION["products"] as $cartItem) {
						    	$rowArray["productID"] = $cartItem["productID"];
						    	$rowArray['title'] = $cartItem["title"];
						    	$rowArray['price'] = $cartItem["price"];
						    	$rowArray['thumbnailPath'] = $cartItem["thumbnailPath"];
						    	$rowArray['quantity'] = $cartItem["quantity"];
						    	array_push($jsonData, $rowArray);
						    }
						    header('Content-Type: application/json');
							echo json_encode($jsonData);
					} else {
						//if the shopping cart is undefiined (non-set), just return an empty array
						header('Content-Type: application/json');
						echo json_encode(array());
					}
				} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
					//only the product ID and the requested quantity are passed via the POST
					$productID = filter_var($_POST["productID"], FILTER_SANITIZE_NUMBER_INT);
					$quantity = filter_var($_POST["quantity"], FILTER_SANITIZE_NUMBER_INT);
					$found = false;
					$message= "";

					if(isset($_SESSION["products"]))  {
					           	//iterate over the shopping cart
					            foreach ($_SESSION["products"] as $k => $v) {
					                if($v["productID"] == $productID) {
					                	//the product already exists in the cart, so just increase the quantity
					                    $_SESSION["products"][$k]["quantity"] = $_SESSION["products"][$k]["quantity"] + $quantity;
					                    $found = true;
					                    $message= $quantity ." more item(s) of this one were added to your shopping cart.";
					                    break;
					                }
					            }
					}
					//if a product with that ID doesn't exit yet in the shopping cart, get its details from the DB and add it to the cart
					if($found==false) {
					            	$con = mysqli_connect("localhost","root","","db_versatile_shop"); 
									if (mysqli_connect_errno()) {
										echo "Failed to connect to MySQL: " . mysqli_connect_error();
									}
					            	$query = "SELECT title, price, thumbnailPath FROM products WHERE productID=".mysqli_real_escape_string($con, $productID)." LIMIT 1";    
									$result = mysqli_query($con, $query);
								    $row = mysqli_fetch_array($result, MYSQL_NUM);
								    $title = $row[0];
								    $price = $row[1];
								    $thumbnailPath = $row[2];
								    mysqli_close($con);
					            	$newProduct = array("productID"=> $productID, "title"=> $title, "price"=> $price, "thumbnailPath"=>$thumbnailPath, "quantity"=>$quantity);
					            	if(isset($_SESSION["products"])) {
										array_push($_SESSION["products"] , $newProduct);
									} else {
										$_SESSION["products"] = array($newProduct);
									}
					            	$message="The item was added to your shopping cart.";
					}
					
					echo $message;
		} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
			if(isset($_SESSION["products"])) {
				//the id of the product for removing is passed via the request url's query params
				if(isset($_GET["productID"])) {
					foreach ($_SESSION["products"] as $k => $v) {
					    if($v["productID"] == $_GET["productID"]) {
					    	unset($_SESSION["products"][$k]);
					        echo "Product successfully removed from the shopping cart";
					        break;
					    }
					}
				} else {
					//if no specific product id is given, remove all items from the shopping cart
					//unset($_SESSION["products"]);
					$_SESSION["products"] = [];
					echo "Your shopping cart is now empty";
				}
			}
		}
?>