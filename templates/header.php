	<nav class="navbar navbar-inverse" role="navigation">
	  <div class="navbar-header">
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	      <span class="sr-only">Toggle navigation</span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>
	    <a id="home-logo" href="index.php"><img src="images/gamepad.png" alt="joystick-home"/></a>
	  </div>
	  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	    <ul class="nav navbar-nav">
	      <li><a href="index.php">Home</a></li>
	      <li><a href="shop.php"><span class="glyphicon glyphicon glyphicon-tags"></span>  Shop</a></li>
	      <li><a href="shopping-cart.php"><span class="glyphicon glyphicon-shopping-cart"></span>  View shopping cart</a></li>
	      <li><a href="search.php"><span class="glyphicon glyphicon-search"></span>  Search</a></li>
	    </ul>
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
	  </div>
	</nav>
