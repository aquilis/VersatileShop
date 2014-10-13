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
        <script>
            /**
             * Displays the cart items (if any) at page load.
             */
            $(document).ready(function () {
                $(".nav li:contains('View shopping cart')").addClass("active");
                loadShoppingCart();
            });
            
            /**
             * Retrieves and displays all products from the shopping cart in a grid.
             * Attaches the handler functions of the remove buttons.
             **/
            function loadShoppingCart() {
                utils.displayAjaxLoader("items-area", "Loading...", false);
                $.getJSON("models/shopping-cart-model.php", function (data) {
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
                                "<a class='remove-from-cart-btn close-delete-btn' productID='" + element.productID + "'><span class='glyphicon glyphicon-remove'></span> Remove from cart</a>" +
                                "</h4></div>" +
                                "<div class=\"panel-body\">" +
                                " <a href='shop.php?productID=" + element.productID + "' class=\"thumbnail shopping-cart-thumbnail\">" +
                                "<img src='" + element.thumbnailPath + "' alt=\"thumbnail\">" +
                                "</a>" +
                                "<p>Quantity: " + element.quantity +
                                "<p>Price for a single game: " + element.price +
                                "<p>Price total: " + (element.price * element.quantity) +
                                "</div>" +
                                "</div>";
                        totalSum += (element.price * element.quantity);

                    });
                    //if there are any products in the cart, show the shopping cart buttons, otherwise, hide them and how an image for an empty cart
                    if (hasProducts) {
                        itemsHtml += "<h4>Total: " + totalSum + "$</h4>";
                        //TODO: why row when there are many rows??
                        $(".row").html(itemsHtml);
                        if (!$("#cart-buttons").is(":visible")) {
                            $("#cart-buttons").show();
                        }
                        //attach a click handler to the empty cart button
                        $("#empty-cart-btn").click(function () {
                            $.ajax({
                                url: "models/shopping-cart-model.php",
                                type: "DELETE",
                            }).done(function (msg) {
                                loadShoppingCart();
                                var html = "<div class=\"alert alert-success\" role=\"alert\">" + msg + "</div>";
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
                            url: "models/shopping-cart-model.php?productID=" + productID,
                            type: "DELETE",
                        }).done(function (msg) {
                            //reload the shopping cart after deleting a product
                            loadShoppingCart();
                            var html = "<div class=\"alert alert-success\" role=\"alert\">" + msg + "</div>";
                            utils.displayAndFadeOutResultsPanel("result-panel", html, "fast");
                        });
                    });
                });
            }
        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-shopping-cart"></span>Your shopping cart</h1>
                <div id="result-panel" class="result-panel-sml"> </div>
                <div id="items-area" class="row">			
                </div>
                <div id="cart-buttons">
                    <button id="empty-cart-btn" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-remove"></span> Empty cart</button>
                    <button class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-usd"></span> To checkout</button>
                </div>
            </div> 
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>