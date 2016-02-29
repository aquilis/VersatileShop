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
	      <li id="header-home"><a href="index.php"><span i18n_label="home"></span></a></li>
	      <li id="header-shop"><a href="shop.php"><span class="glyphicon glyphicon glyphicon-tags"></span><span i18n_label="shop"></span></a></li>
	      <li id="header-cart"><a href="shopping-cart.php"><span class="glyphicon glyphicon-shopping-cart"></span><span i18n_label="shopping.cart"></span></a></li>
	      <li id="header-search"><a href="search.php"><span class="glyphicon glyphicon-search"></span><span i18n_label="search"></span></a></li>
	    </ul>
	    <ul class="nav navbar-nav navbar-right">
	      <?php 
		      if(!isLogged())  {
			      echo "<li><a id='nav-login' href=\"login.php\"><button type=\"button\" class=\"btn btn-primary btn-xs\">
						<span i18n_label=\"login\"></span></button></a></li>";
			   }
	      ?>

	      <li><a href="#"><span i18n_label="about"></span></a></li>
	      <?php
	      	if(isLogged()) {
		       echo "<li class=\"dropdown\">
		        <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".$_SESSION['username']."<b class=\"caret\"></b></a>
		        <ul class=\"dropdown-menu\">
		          <li><a href=\"#\"><span i18n_label=\"profile\"></span></a></li>
		          <li class=\"divide\r\"></li>
		          <li><a href=\"index.php?action=logout\"><span i18n_label=\"logout\"></span></a></li>
		        </ul>
		      </li>";
		      }
	      ?>
	    </ul>
	  </div>
	</nav>
