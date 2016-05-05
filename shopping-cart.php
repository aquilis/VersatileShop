<?php
include 'lib/acc_functions.php';
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
                $(".nav li[id=header-cart]").addClass("active");
                loadShoppingCart();
            });
            
            /**
             * Retrieves and displays all products from the shopping cart in a grid.
             * Attaches the handler functions of the remove buttons.
             **/
            function loadShoppingCart() {
                utils.displayAjaxLoader("items-area", "Loading...", false);
                $.getJSON("services/ShoppingCartService.php", function (data) {
                    var itemsHtml = "";
                    var totalSum = 0;
                    var hasProducts = false;
                    $(data).each(function (index, element) {
                        if (!hasProducts) {
                            hasProducts = true;
                        }
                        itemsHtml +=
                                "<div class=\"panel panel-info\">" +
                                "<div class=\"panel-heading\"><h4>" +
                                element.title +
                                "<a class='remove-from-cart-btn close-delete-btn' productID='" + element.productID + "'>" +
                                "<span class='glyphicon glyphicon-remove'></span> <span i18n_label=\"remove.from.cart\"></span></a>" +
                                "</h4></div>" +
                                "<div class=\"panel-body\">" +
                                " <a href='shop.php?productID=" + element.productID + "' class=\"thumbnail shopping-cart-thumbnail\">" +
                                "<img src='" + element.thumbnailPath + "' alt=\"thumbnail\">" +
                                "</a>" +
                                "<p><span i18n_label=\"quantity\"></span>: " + element.quantity +
                                "<p><span i18n_label=\"price\"></span>: " + element.price +
                                "<p><span i18n_label=\"price.total\"></span>: " + (element.price * element.quantity) +
                                "</div>" +
                                "</div>";
                        totalSum += (element.price * element.quantity);

                    });
                    //if there are any products in the cart, show the shopping cart buttons, otherwise, hide them and how an image for an empty cart
                    if (hasProducts) {
                        itemsHtml += "<h4><span i18n_label=\"total\"></span>: " + totalSum + "$</h4>";
                        //TODO: why row when there are many rows??
                        $(".row").html(itemsHtml);
                        if (!$("#cart-buttons").is(":visible")) {
                            $("#cart-buttons").show();
                        }
                        //attach a click handler to the empty cart button
                        $("#empty-cart-btn").click(function () {
                            $.ajax({
                                url: "services/ShoppingCartService.php",
                                type: "DELETE",
                            }).done(function (msg) {
                                loadShoppingCart();
                                var html = "<div class=\"alert alert-success\" role=\"alert\">" + jQuery.i18n.map[msg.trim()] + "</div>";
                                utils.displayAndFadeOutResultsPanel("result-panel", html, "fast");
                            });
                        });
                    } else {
                        $("#cart-buttons").hide();
                        $(".row").html("<img src=\"images/empty-cart.png\" alt=\"Your shopping cart is empty\"></img>");
                    }
                }).done(function () {
                    //attach the click handler of the delete button
                    $(".remove-from-cart-btn").click(function () {
                        var productID = $(this).attr("productID");
                        $.ajax({
                            url: "services/ShoppingCartService.php?productID=" + productID,
                            type: "DELETE",
                        }).done(function (msg) {
                            //reload the shopping cart after deleting a product
                            loadShoppingCart();
                            var html = "<div class=\"alert alert-success\" role=\"alert\">" + jQuery.i18n.map[msg.trim()] + "</div>";
                            utils.displayAndFadeOutResultsPanel("result-panel", html, "fast");
                        });
                    });
                    languageUtils.applyLabelsToHTML();
                });
            }
        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-shopping-cart"></span><span i18n_label="your.shopping.cart"></span>
                </h1>
                <div id="result-panel" class="result-panel-sml"> </div>
                <div id="items-area" class="row">			
                </div>
                <div id="cart-buttons">
                    <a href="checkout.php"><button class="btn btn-lg btn-primary"> <span class="glyphicon glyphicon-hand-right"></span> <span i18n_label="to.checkout"></span></button></a>
                    <button id="empty-cart-btn" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-remove"></span> <span i18n_label="empty.cart"></span></button>
                </div>
            </div> 
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>