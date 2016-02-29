<?php
include 'lib/acc_functions.php';
?>
<html>
    <head>
        <title>Login</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/language-utils.js"></script>
    </head>
    <script>
        $(document).ready(function () {
            languageUtils.applyLabelsToHTML();
        });
    </script>
    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <h1><span i18n_label="login.to.account"></span> <a href="register.php"><span i18n_label="register.verb"></span></a></h1>
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
                                //XXX This really sux
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
                        echo "<div class=\"alert alert-success\"> +
  						<span i18n_label=\"successfull.registration.message\"></span> +
						</div>";
                    }
                    echo "
		 		   <div class=\"panel panel-primary\">
		 			<div class=\"login-form\">
			    	<form action=\"login.php?action=validate\" method=\"post\">
			        <label><span i18n_label=\"username\"></span></label>
			        <input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\"><br>
			        <label><span i18n_label=\"password\"></span></label>
			        <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\">
			        <label class=\"checkbox\"><input type=\"checkbox\"> <span i18n_label=\"remember.me\"></span></label>
			        <button type=\"submit\" class=\"btn btn-primary\"><span i18n_label=\"login\"></span></button>
			    	</form>
					</div>
					</div>";
                } elseif ($failedLogin == true) {
                    echo "
					<div class=\"panel panel-primary\">
					  <div class=\"alert alert-danger\"><span i18n_label=\"login.invalid.message\"></span></div>
						<div class=\"login-form\">
									<div class=\"form-group has-error\">
								<form action=\"login.php?action=validate\" method=\"post\">
									 <label class=\"control-label\" for=\"inputError\"><span i18n_label=\"username\"></span></label>
									<input class=\"form-control\" type=\"text\" placeholder=\"Username\" name=\"username\" id=\"inputError\"><br>
									 <label class=\"control-label\" for=\"inputError\"><span i18n_label=\"password\"></span></label>
									 <input class=\"form-control\" type=\"password\" placeholder=\"Password\" name=\"pass\" id=\"inputError\">
									 </div> 
									 <label class=\"checkbox\"><input type=\"checkbox\"> <span i18n_label=\"remember.me\"></span></label>
									 <button type=\"submit\" class=\"btn btn-primary\"><span i18n_label=\"login\"></span></button>
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