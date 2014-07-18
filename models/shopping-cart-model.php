<?php	
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
				//The get mehod is used to get all showcase entries in JSON format
				if($_SERVER["REQUEST_METHOD"] == "GET") {
					if(isset($_SESSION["products"])) {
						    $jsonData = array();
						    foreach ($_SESSION["products"] as $cart_itm) {
						    	$rowArray['name'] = $cart_itm["name"];
						    	$rowArray['qty'] = $cart_itm["qty"];
						    	$rowArray['price'] = $cart_itm["price"];
						    	array_push($jsonData, $rowArray);
						    }
						    header('Content-Type: application/json');
							echo json_encode($jsonData);
					} else {
						    echo 'Your Cart is empty';
					}
				} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
					
					   $product_name   = filter_var($_POST["product_name"], FILTER_SANITIZE_STRING); //product code
					   $product_qty    = filter_var($_POST["product_qty"], FILTER_SANITIZE_NUMBER_INT); //product code
					   $product_price  = filter_var($_POST["product_price"], FILTER_SANITIZE_NUMBER_INT);

					        //prepare array for the session variable
					        $new_product = array(array('name'=>$product_name, 'qty'=>$product_qty, 'price'=>$product_price));
					       
					        if(isset($_SESSION["products"])) //if we have the session
					        {
					            $found = false; //set found item to false
					           
					            foreach ($_SESSION["products"] as $cart_itm) {
					                if($cart_itm["name"] == $product_name) {
					                    $product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=>($product_qty + $cart_itm["qty"]), 'price'=>$cart_itm["price"]);
					                    $found = true;
					                } else{
					                    //item doesn't exist in the list, just retrive old info and prepare array for session var
					                    $product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=>$cart_itm["qty"], 'price'=>$cart_itm["price"]);
					                }
					            }
					           
					            if($found == false) //we didn't find item in array
					            {
					                //add new user item in array
					                $_SESSION["products"] = array_merge($product, $new_product);
					            }else{
					                //found user item in array list, and increased the quantity
					                $_SESSION["products"] = $product;
					            }
					           
					        }else{
					            //create a new session var if does not exist
					            $_SESSION["products"] = $new_product;
					        }
       
		} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
			if(isset($_SESSION["products"])) {
				unset($_SESSION["products"]);
			}
		}
?>