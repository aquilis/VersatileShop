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
        <script src="js/utils.js"></script>
        <script>
            $(document).ready(function () {
                /**
                 * Validates the user details from the registration form.
                 * 
                 * @param username is the username
                 * @param password is the password
                 * @param email is the email
                 * @returns a result object with a status and message fields
                 */
                function validateRegistration(username, password, email) {
                    var isValid = true;
                    var validationMessage = "";
                    //first, check if any of the fields is empty
                    if ((username.length === 0) || (password.length === 0) || (email.length === 0)) {
                        return {
                            status: false,
                            message: "Please, fill in the mandatory fields."
                        };
                    }
                    //validate the username using a regex pattern
                    if (!username.match(/^([a-zA-Z0-9_-]){5,15}$/)) {
                        validationMessage += "Username must contain only latin letters or numbers, no spaces and be between 5 and 15 characters long.\n";
                        isValid = false;
                    }
                    //validate the password using a regex pattern
                    if (!password.match(/^([a-zA-Z0-9_-]){6,20}$/)) {
                        validationMessage += "Password must contain only latin letters or numbers, no spaces and be between 6 and 20 characters long.\n";
                        isValid = false;
                    }
                    //validate the e-mail using a regex pattern
                    if (!email.match(/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/)) {
                        validationMessage += "Invalid e-mail format.\n";
                        isValid = false;
                    }
                    return {
                        status: isValid,
                        message: validationMessage
                    };
                }

                /**
                 * Shows a label with the validation errors and highlights the text fields in red.
                 * 
                 * @param validationResult is a result JSON object from a validation having 
                 *                         a status and message fields
                 */
                function highlightErrors(validationResult) {
                    if (validationResult.status === true) {
                        return;
                    }
                    var errors = validationResult.message.replace(/(?:\r\n|\r|\n)/g, "<br/>");
                    var html = "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\"></span> " +
                            errors + "</div>";
                    var resultPanel = $("#result-panel");
                    resultPanel.html(html);
                    $(".login-form").addClass("has-error");
                }

                /**
                 * Attaches a click handler to the register button
                 */
                $("#register-btn").click(function () {
                    var username = $("#username-field").val();
                    var password = $("#password-field").val();
                    var email = $("#email-field").val();
                    var validationResult = validateRegistration(username, password, email);
                    if (validationResult.status === false) {
                        highlightErrors(validationResult);
                        return;
                    }
                    //make the post request
                    $.post("models/registration-model.php", {username: username, password: password, email: email}).done(function (data) {
                        var serverValidationResult = data;
                        if (serverValidationResult.status === false) {
                            highlightErrors(serverValidationResult);
                            return;
                        }
                        //display the successful server response
                        var html = "<div class=\"alert alert-success\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\"></span>" +
                                " Registration successful! You will be now redirected to the <a href='login.php'>login page.</a></div>";
                        var resultPanel = $("#result-panel");
                        resultPanel.fadeIn("fast");
                        resultPanel.html(html);
                        resultPanel.delay(4000).fadeOut(1200, function () {
                            window.location.href = utils.getBaseURL() + "/login.php";
                        });
                    });
                });
            });
        </script>
    </head>

    <body class="paper-textured"> 
        <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <div id="contentArea">
                <h1>Register a new user</h1>
                <div class="panel panel-primary">
                    <div id="result-panel"></div>
                    <div class="form-group login-form">
                        <label>Username*</label>
                        <input id="username-field" class="form-control" type="text" placeholder="Username"><br>
                        <label>E-mail*</label>
                        <input id="email-field" class="form-control" type="text" placeholder="Email"><br>
                        <label>Password*</label>
                        <input id="password-field" class="form-control" type="password" placeholder="Password">
                        <button id="register-btn" type="button" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>