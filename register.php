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
 	<div id="contentArea">
 		<?php
 			$failedReg = false;
			if (isset($_GET['action'])) { 
			  switch (strtolower($_GET['action'])) { 
			    case 'validate': 
					if(validateRegistration($_POST["username"], $_POST["pass"])) {
						createAccount($_POST["username"], $_POST["pass"]);
						header("Location: login.php?newReg=true");
						exit;
					} else {
						$failedReg=true;
					}       
			    break; 
			  } 
			}

	 		if ($failedReg == false) {
	 		echo "
	 			<h1>Register new user</h1>
	 		   <div class=\"panel panel-primary\">
	 			<div class=\"login-form\">
		    	<form action=\"register.php?action=validate\" method=\"post\">
		        <label>Enter your username</label>
		        <input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\"><br>
		        <label>Enter your password</label>
		        <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\">
		        <button type=\"submit\" class=\"btn btn-primary\">Register</button>
		    	</form>
				</div>
				</div>";
			} elseif ($failedReg == true) {
				echo "
					<h1>Register new user</h1>
					<div class=\"panel panel-primary\">
					  <div class=\"alert alert-danger\">".$_SESSION['failedRegMessage']."</div>
						<div class=\"login-form\">
									<div class=\"form-group has-error\">
								<form action=\"register.php?action=validate\" method=\"post\">
									 <label class=\"control-label\" for=\"inputError\">Enter your username</label>
									 <input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\" id=\"inputError\"><br>
									 <label class=\"control-label\" for=\"inputError\">Enter your password</label>
									 <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\" id=\"inputError\">
									 </div> 
									 <button type=\"submit\" class=\"btn btn-primary\">Register</button>
								</form>

						</div>
					</div>";			
			}
 		?>
</div>
</div>
</body>

</html>