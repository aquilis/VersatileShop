<?php
include 'lib/utils.php';
?>
<html>
    <head>
        <title>Your shopping cart</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/language-utils.js"></script>
        <script>
            /**
             * Displays the cart items (if any) at page load.
             */
            $(document).ready(function () {
                languageUtils.applyLabelsToHTML(utils.initializeHeaderBehaviour);
                loadCartProducts();

                $(".address-switch input").change(function() {
                    $(".shipping-address-form").slideToggle("medium");
                });

                $("#confirm-btn").click(function () {
                    var orderDetails = {
                        additionalInfo: $(".additional-order-info").val()
                    };
                    if($('input[id=use-my-address]:checked').val()) {
                        orderDetails.useMyAddress = true;
                    } else {
                        orderDetails.useMyAddress = false;
                        orderDetails.newAddress = {
                            email: $("#email-field").val(),
                            town: $("#town-field").val(),
                            zipCode: $("#zip-field").val(),
                            address: $("#address-field").val(),
                            phone: $("#phone-field").val()
                        };
                    }
                    $.post("services/OrdersService.php", orderDetails).done(function (data) {
                        var serverValidationResult = data;
                        if (serverValidationResult.status === false) {
                            if (serverValidationResult.authenticationFailed === true) {
                                window.location.href = utils.getBaseURL() + "/login.php";
                            } else if (serverValidationResult.errorsMapping) {
                                highlightProductErrors(serverValidationResult);
                            } else {
                                highlightAddressErrors(serverValidationResult);
                            }
                            return;
                        } else {
                            window.location.href = utils.getBaseURL() + "/my-orders.php";
                        }
                    });
                });

            });

            /**
             * Loads and displays all products from the shopping cart so that the user can have a final glimpse of what
             * they are about to order and pay for.
             */
            function loadCartProducts() {
                utils.displayAjaxLoader("items-area", "Loading...", false);
                $.getJSON("services/ShoppingCartService.php", function (data) {
                    var itemsHtml = "<div class=\"row head-row\">" +
                    "<div class=\"col-md-4\">" + jQuery.i18n.map["product.name"] + "</div>" +
                    "<div class=\"col-md-4\">" + jQuery.i18n.map["quantity"] + "</div>"+
                    "<div class=\"col-md-4\">" + jQuery.i18n.map["price"] + "</div>"+
                    "</div>";
                    var totalSum = 0;
                    var hasProducts = false;
                    $(data).each(function (index, element) {
                        totalSum+= (element.price * element.quantity);
                        itemsHtml+= "<div class=\"row\">" +
                            "<div class=\"col-md-4\"><a href='shop.php?productID=" + element.productID + "'>" +
                            element.title + "</a></div>" +
                            "<div class=\"col-md-4\">" + element.quantity + "</div>"+
                            "<div class=\"col-md-4\">" + (element.price * element.quantity) + "</div>"+
                            "</div>";
                    });
                    $("#orders-section").html(itemsHtml);
                    $(".final-amount-to-pay").append(": " + totalSum);
                })
            };

            /**
             * Highligths the product-related errors such as not enough in stock, not available.
             */
            function highlightProductErrors(validationResult) {
                var errors = "";
                if(validationResult.errorsMapping) {
                    $.each(validationResult.errorsMapping, function(productTitle, errorMessageCode) {
                        errors+= jQuery.i18n.map[errorMessageCode] + " " +  productTitle  + "<br/>";
                    });
                }
                var html = "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\"></span> " +
                    errors + "</div>";
                var resultPanel = $("#product-result-panel");
                resultPanel.html(html);
                //scroll  to top of page, so that the user can see the errors
                window.scrollTo(0,0);
            }

            /**
             * Highlights the address validation errors in the specified error section.
             */
            function highlightAddressErrors(validationResult) {
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
                $(".shipping-address-form").addClass("has-error");
            }


        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-shopping-cart"></span><span i18n_label="confirm.order.details.heading"></span></h1>
                <div id="product-result-panel"></div>
                <div id="orders-section" class="well row">
                </div>
                <div id="address-section" class=" well row">
                    <h3><span i18n_label="select.shipping.address"></span></h3>
                    <div class="address-switch">
                        <input id="use-my-address" type="radio" name="address-type" checked="checked"><span i18n_label="use.my.account.address"></span><br>
                        <input id="use-new-address" type="radio" name="address-type"><span i18n_label="use.new.address"> <br>
                    </div>
                    <div class="shipping-address-form" style="display: none">
                        <div id="result-panel"></div>
                        <label><span i18n_label="email"></span></label>
                        <input id="email-field" class="form-control" type="text"><br>
                        <label><span i18n_label="town"></span></label>
                        <input id="town-field" class="form-control input-sml" type="text"><br>
                        <label><span i18n_label="zip.code"></span></label>
                        <input id="zip-field" class="form-control input-sml" type="text"><br>
                        <label><span i18n_label="address"></span></label>
                        <input id="address-field" class="form-control" type="text"><br>
                        <label><span i18n_label="phone.number"></span></label>
                        <input id="phone-field" class="form-control" type="text">
                    </div>
                </div>
                <div id="info-section" class="well row">
                    <h3><span i18n_label="additional.order.info"></span></h3>
                    <textarea class="additional-order-info form-control"></textarea>
                    <h3 class="final-amount-to-pay"><span i18n_label="final.amount.to.pay"></span></h3>
                </div>
                <div id="bottom-buttons">
                    <button id="confirm-btn" class="btn btn-lg btn-primary"> <span i18n_label="confirm.order"></span></button>
                    <a href="shopping-cart.php"><button class="btn btn-lg btn-default"><span i18n_label="button.back"></span></button></a>
                </div>
            </div> 
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>