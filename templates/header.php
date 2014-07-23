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
	    <a id="home-logo" href="index.php"><img src="images/gamepad.png" alt="joystick-home"/></a>
	  </div>

	  <!-- Collect the nav links, forms, and other content for toggling -->
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav">
	      <li><a href="index.php">Home</a></li>
	      <li><a href="shop.php"><span class="glyphicon glyphicon glyphicon-tags"></span>  Shop</a></li>
	      <li><a href="shopping-cart.php"><span class="glyphicon glyphicon-shopping-cart"></span>  View shopping cart</a></li>
	    </ul>
	    <form class="navbar-form navbar-left" role="search">
	      <div class="form-group">
	        <input type="text" class="form-control" placeholder="Search">
	      </div>
	      <button type="submit" class="btn btn-default">Submit</button>
	    </form>
	    <ul class="nav navbar-nav navbar-right">
	      <li><a href="forum.php">Talk <span class="label label-default">beta</span></a></li>
	      <li><a href="#">About</a></li>
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
