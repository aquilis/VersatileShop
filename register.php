<?php
include 'lib/acc_functions.php';
?>
<html>
    <head>
        <title>Register</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
    </head>

    <body class="paper-textured"> 
       <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <div id="contentArea">
                <?php
                $failedReg = false;
                if (isset($_GET['action'])) {
                    switch (strtolower($_GET['action'])) {
                        case 'validate':
                            if (validateRegistration($_POST["username"], $_POST["pass"])) {
                                createAccount($_POST["username"], $_POST["pass"]);
                                header("Location: login.php?newReg=true");
                                exit;
                            } else {
                                $failedReg = true;
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
					  <div class=\"alert alert-danger\">" . $_SESSION['failedRegMessage'] . "</div>
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