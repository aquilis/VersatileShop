<?php	
include "../lib/acc_functions.php"; 
//start session, if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//the number of posts to be displayed at each page in the front end
$postsPerPage = 5;

/**
* Validates that the given post content is between 5 and 200 characters long
**/
function validatePostLength($content) {
	if ((strlen($content) < 5) || (strlen($content) > 200)) {
		echo "The post should be between 5 and 200 characters long";
		return false;
	}
	return true;
}

/**
* Posts to the forum the given content with the currently logged username taking the current date.
**/
function postToForum($content) {
	//validate the post's length
	if(!validatePostLength($content)) {
		return false;
	}
	//connect to the DB
	$con = mysqli_connect("localhost","root","","db_forum"); 
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		return false;
	}
	mysqli_query($con,"INSERT INTO entries (author, date, content, badges)
					   VALUES ('".mysqli_real_escape_string($con, $_SESSION['username'])."', '".date("y/m/d H:i:s")."','"
					   	. mysqli_real_escape_string($con, escapeShellAndHtml($content)) ."','".getBadges($_SESSION['username'])."')");
	mysqli_close($con);
	return true;
}

/**
* If the current user is logged as admin, deletes the post with the name/date from the $_POST params
**/
function deletePost() {
	if((isset($_SESSION['isAdmin']))&&(isset($_POST['delName']))&&(isset($_POST['delDate']))) {
		$delName = $_POST["delName"];
		$delDate = $_POST["delDate"];
		$con = mysqli_connect("localhost","root","","db_forum"); 
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = "DELETE FROM entries
				WHERE author='".$delName."' AND date='".$delDate."'; ";
		$result = mysqli_query($con, $sql) or trigger_error("Query Failed: " . mysql_error());
		mysqli_close($con);
		return true;
	} else {
		return false;
	}
}

/*
** 	Gets all badges from the database for the given user and return them as html string
**/
function getBadges($username) {
	$con = mysqli_connect("localhost","root","","db_login"); 
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$query = mysqli_query($con, "SELECT badges FROM users WHERE username = '".$username."' LIMIT 1");
	$row = mysqli_fetch_assoc($query);
	$badges = $row['badges'];
	mysqli_free_result($query);
	mysqli_close($con);
	return $badges;
}

/**
* Gets the total number of posts in the forum database.
**/
function getTotalNumberOfPosts() {
	$con = mysqli_connect("localhost","root","","db_forum"); 
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$query = mysqli_query($con, "SELECT count(id) FROM entries");
	$row = mysqli_fetch_array($query, MYSQL_NUM);
	return $row[0];
}

				/**
				*	Implements a REST service that handles all server request methods to the forum model
				**/
				if($_SERVER["REQUEST_METHOD"] == "GET") {
					if ((isset($_GET["action"]))&&($_GET["action"] == "pagingData")) {
						//wrap in json and return the data needed for paging - the number of posts and the posts per page
						header('Content-Type: application/json');
						echo json_encode(array('numberOfPosts' => getTotalNumberOfPosts(), "postsPerPage" => $postsPerPage));
					} else if (!isset($_GET["action"])) {
						//constructs the offset parameter for the paging
						if(isset($_GET['page'])) {
							$offset = $postsPerPage * ($_GET['page'] - 1) ;
						} else {
						   $offset = 0;
						}
						// $reversed = true;
						// if(isset($_GET["asc"])) {
						// 	$reversed = true;
						// } else if(isset($_GET["desc"])) {
						// 	$reversed = false;
						// }
						$con = mysqli_connect("localhost","root","","db_forum"); 
						$mysqlquery = "SELECT * FROM entries ORDER BY id DESC LIMIT $offset, $postsPerPage";
						if (mysqli_connect_errno()) {
								echo "Failed to connect to MySQL: " . mysqli_connect_error();
						}
					    $result = mysqli_query($con, $mysqlquery);
					    $jsonData = array();
		   				while($row = mysqli_fetch_array($result, MYSQL_NUM)) {
					   		$rowArray['author'] = $row[1];
	   						$rowArray['date'] = $row[2];
	    					$rowArray['content'] = $row[3];
	    					$rowArray['badges'] = $row[4];
	    					array_push($jsonData, $rowArray);
						}
						header('Content-Type: application/json');
						echo json_encode($jsonData);
					}
				} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
					if($_POST["action"] == "delete") {
						//Handles the deleting of a certain froum post and returns a json wrapped status message
						header('Content-Type: application/json');
						if(deletePost()) {
							echo json_encode(array('status' => "ok", "message" => "The post was successfully deleted"));
						} else {
							echo json_encode(array('status' => "error", "message" => "There was an error. The post was NOT deleted"));
						}
					} else if ($_POST["action"] == "post") {
						//Handles the posting of a new user post into the DB and returns a json wrapped status message
						header('Content-Type: application/json');
						if(postToForum($_POST["content"])) {
							echo json_encode(array('status' => "ok", "message" => "Post succeessfully saved"));
						} else {
							echo json_encode(array('status' => "error", "message" => "There was an error. Your post was not saved"));
						}
					}
				}
?>