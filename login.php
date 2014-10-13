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

    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <h1>Login to your account or <a href="register.php">register</a></h1>
            <div id="contentArea">
                <?php
                $failedLogin = false;
                if (isset($_GET['action'])) {
                    switch (strtolower($_GET['action'])) {
                        case 'validate':
                            if (validateLogin($_POST["username"], $_POST["pass"])) {
                                $_SESSION['isLogged'] = 1;
                                $_SESSION['username'] = $_POST["username"];
                                if (isAdmin(trim($_POST["username"]))) {
                                    $_SESSION['isAdmin'] = 1;
                                }
                                //reset the shopping cart when a new user logs in
                                //TODO: is this the right way??
                                if (isset($_SESSION['products'])) {
                                    unset($_SESSION['products']);
                                }
                                setcookie('lastLogin', date("d/m/y H:i:s"), 60 * 60 * 24 * 60 + time());
                                header("Location: index.php");
                                exit;
                            } else {
                                $_SESSION['isLogged'] = 0;
                                $failedLogin = true;
                            }
                            break;
                    }
                }

                if (($failedLogin == false) && (!isLogged())) {
                    if (isset($_GET['newReg'])) {
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
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>