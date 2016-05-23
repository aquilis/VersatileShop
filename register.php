<?php
include 'lib/utils.php';
?>
<html>
    <head>
        <title>Register</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/language-utils.js"></script>

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
                            message: "fill.in.mandatory.fields",
                            errorInMandatoryFields: true
                        };
                    }
                    //validate the username using a regex pattern
                    if (!username.match(/^([a-zA-Z0-9_-]){5,15}$/)) {
                        validationMessage += "username.registration.fail\n";
                        isValid = false;
                    }
                    //validate the password using a regex pattern
                    if (!password.match(/^([a-zA-Z0-9_-]){6,20}$/)) {
                        validationMessage += "password.registration.fail\n";
                        isValid = false;
                    }
                    //validate the e-mail using a regex pattern
                    if (!email.match(/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/)) {
                        validationMessage += "email.registration.fail\n";
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
                        validationMessage += "first.name.registration.fail\n";
                        isValid = false;
                    }
                    //validate the last name using a regex pattern
                    if ((typeof(lastName)!=="undefined") && (lastName.length > 0) && (!lastName.match(/^([a-zA-Z\s]){0,50}$/))) {
                        validationMessage += "last.name.registration.fail\n";
                        isValid = false;
                    }
                    //validate the town using a regex pattern
                    if ((typeof(town)!=="undefined") && (town.length > 0) && (!town.match(/^([a-zA-Z\s]){0,50}$/))) {
                        validationMessage += "town.registration.fail\n";
                        isValid = false;
                    }
                    //validate the zip code using a regex pattern
                    if ((typeof(zipCode)!=="undefined") && (zipCode.length > 0) && (!zipCode.match(/^([a-zA-Z0-9\s\-]){0,12}$/))) {
                        validationMessage += "zip.code.registration.fail\n";
                        isValid = false;
                    }
                    //validate the address using a regex pattern
                    if ((typeof(address)!=="undefined") && (address.length > 0) && (!address.match(/^([a-zA-Z0-9\s\_\-\,\.]){0,70}$/))) {
                        validationMessage += "address.registration.fail\n";
                        isValid = false;
                    }
                     //validate the phone using a regex pattern
                    if ((typeof(phone)!=="undefined") && (phone.length > 0) && (!phone.match(/^([0-9+]){0,20}$/))) {
                        validationMessage += "phone.number.registration.fail\n";
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
                    var split = validationResult.message.split(/(?:\r\n|\r|\n)/g);
                    var errors = "";
                    $.each(split, function(index, value) {
                        if(value.length > 0) {
                            errors+= jQuery.i18n.map[value] + "<br/>";
                        }
                    });
                    var html = "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\"></span> " +
                            errors + "</div>";
                    var resultPanel = $("#result-panel");
                    resultPanel.html(html);
                    //scroll  to top of page, so that the user can see the errors
                    window.scrollTo(0,0);
                    if(validationResult.errorInMandatoryFields) {
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
                            jQuery.i18n.map['registration.successfull.redirect'] +  "<a href='login.php'>" +
                             jQuery.i18n.map['login'] + "</a></div>";
                        var resultPanel = $("#result-panel");
                        resultPanel.fadeIn("fast");
                        resultPanel.html(html);
                        window.scrollTo(0,0);
                        resultPanel.delay(4000).fadeOut(1200, function () {
                            window.location.href = utils.getBaseURL() + "/login.php";
                        });
                    });
                });
                languageUtils.applyLabelsToHTML(utils.initializeHeaderBehaviour);
            });
        </script>
    </head>

    <body class="paper-textured"> 
        <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-user"></span> <span i18n_label="register.new.user"></span></h1>
                <p><span i18n_label="fields.asterisk.mandatory"></span></p>
                <div class="panel panel-primary authentication-panel">
                    <div id="result-panel"></div>
                    <div class="form-group registration-form">
                        <div class="mandatory-details">
                            <label><span i18n_label="username"></span>*</label>
                            <input id="username-field" class="form-control" type="text"><br>
                            <label><span i18n_label="email"></span>*</label>
                            <input id="email-field" class="form-control" type="text"><br>
                            <label><span i18n_label="password"></span>*</label>
                            <input id="password-field" class="form-control" type="password">
                        </div>
                        <hr>
                        <h5><span i18n_label="additional.user.details.heading"></span></h5>
                        <div class="additional-details">
                            <label><span i18n_label="first.name"></span></label>
                            <input id="first-name-field" class="form-control" type="text"><br>
                            <label><span i18n_label="last.name"></span></label>
                            <input id="last-name-field" class="form-control" type="text"><br>
                            <label><span i18n_label="town"></span></label>
                            <input id="town-field" class="form-control input-sml" type="text"><br>
                            <label><span i18n_label="zip.code"></span></label>
                            <input id="zip-field" class="form-control input-sml" type="text"><br>
                            <label><span i18n_label="address"></span></label>
                            <input id="address-field" class="form-control" type="text"><br>
                            <label><span i18n_label="phone.number"></span></label>
                            <input id="phone-field" class="form-control" type="text">
                        </div>
                        <button id="register-btn" type="button" class="btn btn-primary"><span i18n_label="register.button"></span></button>
                    </div>

                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>