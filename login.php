<?php
include 'lib/acc_functions.php'; 
?>
<html>
<head>
<title>Aquilis's dynamic web page</title>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.js"></script>
</head>

<body>
		<!-- ============== NAVIGATION ==============================-->
	<nav class="navbar navbar-inverse" role="navigation">
	  <!-- Brand and toggle get grouped for better mobile display -->
	  <div class="navbar-header">
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	      <span class="sr-only">Toggle navigation</span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>
	    <a class="navbar-brand" href="index.php">Infinigames &copy</a>
	  </div>

	  <!-- Collect the nav links, forms, and other content for toggling -->
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav">
	      <li><a href="index.php">Home</a></li>
	      <li><a href="showcase.php">Showcase</a></li>
	      <li class="dropdown">
	        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Games <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li><a href="#">Meme battlefronts</a></li>
	          <li><a href="#">Meme battlefronts II</a></li>
	          <li class="divider"></li>
	          <li><a href="#">Blaststorm</a></li>
	          <li class="divider"></li>
	          <li><a href="#">Junkman academy</a></li>
	        </ul>
	      </li>
	    </ul>
	    <form class="navbar-form navbar-left" role="search">
	      <div class="form-group">
	        <input type="text" class="form-control" placeholder="Search">
	      </div>
	      <button type="submit" class="btn btn-default">Submit</button>
	    </form>
	    <ul class="nav navbar-nav navbar-right">
	      <li><a href="#">Forum <span class="label label-default">New</span></a></li>
	      <li><a href="#">About</a></li>
	      <li class="dropdown">
	        <a href="#" class="dropdown-toggle" data-toggle="dropdown">More <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li><a href="#">Contribute</a></li>
	          <li><a href="#">Contact us</a></li>
	          <li class="divider"></li>
	          <li><a href="#">Careers</a></li>
	        </ul>
	      </li>
	      <?php
	      	if(isLogged())  {
		       echo "<li class=\"dropdown\">
		        <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".$_SESSION['username']."<b class=\"caret\"></b></a>
		        <ul class=\"dropdown-menu\">
		          <li><a href=\"#\">Profile</a></li>
		          <li class=\"divide\r\"></li>
		          <li><a href=\"index.php?action=logout\">Log out</a></li>
		        </ul>
		      </li>";
		      }
	      ?>
	    </ul>
	  </div><!-- /.navbar-collapse -->
	</nav>
	<!-- ============== END OF NAVIGATION ==============================-->

  <div id="mainColumn">
  	<h1>Login to your account</h1>
 	<div id="contentArea">
 		<?php
 			$failedLogin = false;
			if (isset($_GET['action'])) { 
			  switch (strtolower($_GET['action'])) { 
			    case 'validate':
					if(validateLogin($_POST["username"], $_POST["pass"])) {
						$_SESSION['isLogged'] = 1;
						$_SESSION['username'] = $_POST["username"];
						if(isAdmin(trim($_POST["username"]))) {
							$_SESSION['isAdmin'] = 1;
						}
						setcookie('lastLogin', date("d/m/y H:i:s"), 60 * 60 * 24 * 60 + time());
						header("Location: index.php");
						exit;
					} else {
						$_SESSION['isLogged'] = 0;
						$failedLogin=true;
					}       
			    break; 
			  } 
			}

	 		if (($failedLogin == false) && (!isLogged())) {
	 			if(isset($_GET['newReg'])){
	 				echo "<div class=\"alert alert-success\">
  						Well done! You can now login to your account.
						</div>";
	 			}
		 		echo "
		 		   <div class=\"panel panel-primary\">
		 			<div class=\"login-form\">
			    	<form action=\"login.php?action=validate\" method=\"post\">
			        <label>Username</label>
			        <input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\"><br>
			        <label>Password</label>
			        <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\">
			        <label class=\"checkbox\"><input type=\"checkbox\"> Remember me</label>
			        <button type=\"submit\" class=\"btn btn-primary\">Login</button>
			    	</form>
					</div>
					</div>";
			} elseif ($failedLogin == true) {
				echo "
					<div class=\"panel panel-primary\">
					  <div class=\"alert alert-danger\">Oops.. Seems like you typed in a wrong username or password. Please, try again.</div>
						<div class=\"login-form\">
									<div class=\"form-group has-error\">
								<form action=\"login.php?action=validate\" method=\"post\">
									 <label class=\"control-label\" for=\"inputError\">Username</label>
									<input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\" id=\"inputError\"><br>
									 <label class=\"control-label\" for=\"inputError\">Password</label>
									 <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\" id=\"inputError\">
									 </div> 
									 <label class=\"checkbox\"><input type=\"checkbox\"> Remember me</label>
									 <button type=\"submit\" class=\"btn btn-primary\">Login</button>
								</form>

						</div>
					</div>";			
			}
 		?>
</div> <!-- content area -->
</div> <!--main column -->
<footer>
			<p>Webmaster, developer: Aquilis</p>
			<p>Powered by: Bootstrap v3.0.3, PHP 5.5.0</p>
			<p>Infinigames &copy 2013 All right reserved</p>
		</footer>
</body>

</html>