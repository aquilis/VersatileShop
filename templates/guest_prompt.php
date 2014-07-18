<?php
 	//if the user is not logged, put the login/register prompt for guests 
	 if (!isLogged()) {
		 	echo "<div class=\"panel panel-primary\">
	 				<div class=\"panel-heading\">
					<h3 class=\"panel-title\">You are currently a guest.</h3>
					</div>
					<div class=\"panel-body\"> 
	 				Please, <a href=\"login.php\">login</a> or <a href=\"register.php\">register</a>
	 				</div>
	 				</div>";	
	 }
?>