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
                 * @param userDetails 
                 *                  is an object containing all user details 
                 *                  from the form
                 * 
                 * @returns a result object with a status and message fields
                 */
                function validateRegistration(userDetails) {
                    var username = userDetails.username;
                    var password = userDetails.password;
                    var email = userDetails.email;
                    var firstName = userDetails.firstName;
                    var lastName = userDetails.lastName;
                    var town = userDetails.town;
                    var zipCode = userDetails.zipCode;
                    var address = userDetails.address;
                    var phone = userDetails.phone;
 
                    var isValid = true;
                    var validationMessage = "";
                    //first, check if any of the fields is empty
                    if ((username.length === 0) || (password.length === 0) || (email.length === 0)) {
                        return {
                            status: false,
                            message: "Please, fill in the mandatory fields.",
                            errorInMandatoryFields: true
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
                    //if the mandatory details are not valid, don't validate the additional
                    if(!isValid) {
                        return {
                            status: isValid,
                            message: validationMessage,
                            errorInMandatoryFields: true
                        };
                    }
                    //validate the first name using a regex pattern
                    if ((typeof(firstName)!=="undefined") && (firstName.length > 0) && (!firstName.match(/^([a-zA-Z\s]){0,50}$/))) {
                        validationMessage += "First name can contain only latin letters, spaces and can be no more than 50 characters long.\n";
                        isValid = false;
                    }
                    //validate the last name using a regex pattern
                    if ((typeof(lastName)!=="undefined") && (lastName.length > 0) && (!lastName.match(/^([a-zA-Z\s]){0,50}$/))) {
                        validationMessage += "Last name can contain only latin letters, spaces and can be no more than 50 characters long.\n";
                        isValid = false;
                    }
                    //validate the town using a regex pattern
                    if ((typeof(town)!=="undefined") && (town.length > 0) && (!town.match(/^([a-zA-Z\s]){0,50}$/))) {
                        validationMessage += "Town can contain only latin letters, spaces and can be no more than 50 characters long.\n";
                        isValid = false;
                    }
                    //validate the zip code using a regex pattern
                    if ((typeof(zipCode)!=="undefined") && (zipCode.length > 0) && (!zipCode.match(/^([a-zA-Z0-9\s\-]){0,12}$/))) {
                        validationMessage += "ZIP code can contain only numbers, latin letters, spaces, dashes and can be no more than 12 characters long.\n";
                        isValid = false;
                    }
                    //validate the address using a regex pattern
                    if ((typeof(address)!=="undefined") && (address.length > 0) && (!address.match(/^([a-zA-Z0-9\s\_\-\,\.]){0,70}$/))) {
                        validationMessage += "Address must not contain special characters, except ,.-_ and can be no more than 70 characters long.\n";
                        isValid = false;
                    }
                     //validate the phone using a regex pattern
                    if ((typeof(phone)!=="undefined") && (phone.length > 0) && (!phone.match(/^([0-9+]){0,20}$/))) {
                        validationMessage += "Phone number can contain only digits, '+' sign and can be no more than 20 characters long.\n";
                        isValid = false;
                    }
                    return {
                            status: isValid,
                            message: validationMessage,
                            errorInMandatoryFields: false
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
                    //scroll  to top of page, so that the user can see the errors
                    window.scrollTo(0,0);
                    if(typeof(validationResult.errorInMandatoryFields)!=="undefined" && validationResult.errorInMandatoryFields===true) {
                        $(".mandatory-details").addClass("has-error");
                        $(".additional-details").removeClass("has-error");
                    } else {
                        $(".additional-details").addClass("has-error");
                        $(".mandatory-details").removeClass("has-error");
                    }  
                }

                /**
                 * Attaches a click handler to the register button
                 */
                $("#register-btn").click(function () {
                    var userDetails = {
                        username: $("#username-field").val(),
                        password: $("#password-field").val(),
                        email: $("#email-field").val(),
                        firstName: $("#first-name-field").val(),
                        lastName: $("#last-name-field").val(),
                        town: $("#town-field").val(),
                        zipCode: $("#zip-field").val(),
                        address: $("#address-field").val(),
                        phone: $("#phone-field").val()
                    };
                    var validationResult = validateRegistration(userDetails);
                    if (validationResult.status === false) {
                        highlightErrors(validationResult);
                        return;
                    }
                    //if the front-end validation passes, make the post request to the server
                    $.post("services/RegistrationService.php", userDetails).done(function (data) {
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
                        window.scrollTo(0,0);
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
                <h1><span class="glyphicon glyphicon-user"></span> Register a new user</h1>
                <p>Fields with * are mandatory</p>
                <div class="panel panel-primary authentication-panel">
                    <div id="result-panel"></div>
                    <div class="form-group login-form">
                        <div class="mandatory-details">
                            <label>Username*</label>
                            <input id="username-field" class="form-control" type="text" placeholder="Username"><br>
                            <label>E-mail*</label>
                            <input id="email-field" class="form-control" type="text" placeholder="Email"><br>
                            <label>Password*</label>
                            <input id="password-field" class="form-control" type="password" placeholder="Password">
                        </div>
                        <hr>
                        <h5>Additional user details (not mandatory, but recommended)</h5>
                        <div class="additional-details">
                            <label>First name</label>
                            <input id="first-name-field" class="form-control" type="text" placeholder="First name"><br>
                            <label>Last name</label>
                            <input id="last-name-field" class="form-control" type="text" placeholder="Last name"><br>
                            <label>Town</label>
                            <input id="town-field" class="form-control input-sml" type="text" placeholder="Town"><br>
                            <label>ZIP (Postal) code</label>
                            <input id="zip-field" class="form-control input-sml" type="text" placeholder="ZIP (Postal) code"><br>
                            <label>Address</label>
                            <input id="address-field" class="form-control" type="text" placeholder="Address"><br>
                            <label>Phone</label>
                            <input id="phone-field" class="form-control" type="text" placeholder="Phone">
                        </div>
                        <button id="register-btn" type="button" class="btn btn-primary">Register</button>
                    </div>

                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>