<?php
include_once 'lib/utils.php';

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
        <script src="js/utils.js"></script>
    </head>
    <script>
        $(document).ready(function () {
            languageUtils.applyLabelsToHTML();
            $("#inputUsername").attr("placeholder", jQuery.i18n.map['username']);
            $("#inputPassword").attr("placeholder", jQuery.i18n.map['password']);
            $("#login-btn").click(function() {

                var userDetails = {
                    username : $("#inputUsername").val(),
                    password : $("#inputPassword").val()
                }

                $.post("services/AuthenticationService.php?action=authenticate", userDetails).done(function (data) {
                    var serverValidationResult = data;
                    var resultPanel = $("#result-panel");
                    if (serverValidationResult.status === false) {
                        var html = "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\"></span> " +
                            jQuery.i18n.map[serverValidationResult.message.trim()] + "</div>";
                        resultPanel.fadeIn("fast");
                        resultPanel.html(html);
                        window.scrollTo(0,0);
                        return;
                    }
                    //display the successful server response
                    var html = "<div class=\"alert alert-success\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\"></span>" +
                        jQuery.i18n.map[serverValidationResult.message.trim()];
                    resultPanel.fadeIn("fast");
                    resultPanel.html(html);
                    window.scrollTo(0,0);
                    resultPanel.delay(1500).fadeOut(1200, function () {
                        window.location.href = utils.getBaseURL() + "/index.php";
                    });
                });
            })

        });
    </script>
    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
        <div class="container">
            <div class="form-group authentication-form">
                <div id="result-panel"></div>
                <h2 class="form-signin-heading"><span i18n_label="login.to.account"></span> <a href="register.php"><span i18n_label="register.verb"></span></a></h2>
                <input type="text" id="inputUsername" class="form-control" required autofocus>
                <input type="password" id="inputPassword" class="form-control" required>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me"> <span i18n_label="remember.me"></span>
                    </label>
                </div>
                <button id="login-btn" class="btn btn-lg btn-primary btn-block"><span i18n_label="login"></span></button>
            </div>
        </div>
        </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>