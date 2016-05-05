<?php
include 'lib/utils.php';
if(!isLogged()) {
    header("Location: login.php");
    die();
}
?>
<html>
    <head>
        <title>My orders</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/language-utils.js"></script>
        <script>
            var ORDERS_ACTIVE = "active";
            var ORDERS_ENDED = "ended";
            var ORDERS_ALL = "all";

            $(document).ready(function () {
                $(".nav li[id=header-orders]").addClass("active");

                loadOrders(ORDERS_ACTIVE);

                $(".show-active").click(function() {
                    loadOrders(ORDERS_ACTIVE);
                });
                $(".show-ended").click(function() {
                    loadOrders(ORDERS_ENDED);
                });
                $(".show-all").click(function() {
                    loadOrders(ORDERS_ALL);
                });
            });

            /**
             * Loads the orders for the user, according to the passed state param.
             *
             * @param showOrdersInState
             *                          indicates if active, ended, or all orders have to be loaded
             */
            function loadOrders(showOrdersInState) {
                utils.displayAjaxLoader("items-area", "Loading...", false);
                var serviceQueryParam = "myActiveOrders";
                switch(showOrdersInState) {
                    case ORDERS_ACTIVE:
                        serviceQueryParam = "myActiveOrders";
                        break;
                    case ORDERS_ENDED:
                        serviceQueryParam = "myEndedOrders";
                        break;
                    case ORDERS_ALL:
                        serviceQueryParam = "myAllOrders";
                        break;
                    default:
                        serviceQueryParam = "myAllOrders"
                }
                $.getJSON("services/OrdersService.php?action=" + serviceQueryParam, function (data) {
                    var itemsHtml = "";
                    var totalSum = 0;
                    $(data).each(function (index, element) {
                        //the order may not be shipped yet. So put a "N/A" instead of an empty date
                        var shippingDate = element.shippingDate;
                        if(shippingDate.indexOf("0000-00-00") > -1) {
                            shippingDate = "<span class='glyphicon glyphicon-remove-sign'></span>";
                        }
                        //the status label has different color according to the order status
                        var labelClass = "label-primary";
                        if(element.status.indexOf("shipped") > -1) {
                            labelClass = "label-success";
                        } else if (element.status.indexOf("rejected") > -1) {
                            labelClass = "label-danger";
                        } else if (element.status.indexOf("received") > -1) {
                            labelClass = "label-default";
                        }

                        itemsHtml+= "<div class=\"panel panel-info order-panel\">" +
                                    "<div class=\"panel-heading\"><h4>" +
                            "<span i18n_label=\"order.state\"></span>   " +
                            "<span class=\"label " + labelClass + "\"><span i18n_label=\"" + element.status +  "\"></span></span>" +
                        "</h4>" +
                        "</div>" +
                        "<div class=\"panel-body\">" +
                            "<table class=\"table\" cellpadding=\"24\" >"+
                            "<tr>" +
                            "<td rowspan=\"2\">" +
                            "<a href='shop.php?productID=" + element.productID + "' class=\"thumbnail shopping-cart-thumbnail\">"+
                            "<img src='"  + element.thumbnailPath +  "' alt=\"thumbnail\">" +
                            "</a>" +
                            "</td>" +
                            "<td>" +
                            "<a href='shop.php?productID=" + element.productID + "' class=\"thumbnail shopping-cart-thumbnail\">"+
                                element.title + "</a>" +
                            "</td>" +
                            "<td>" +
                            "<span i18n_label=\"quantity\"></span>: </br>" +
                                 element.quantity +
                            "</td>"+
                            "<td>" +
                            "<span i18n_label=\"ordered.on\"></span>:</br>" +
                                element.orderDate +
                            "</td>"+
                            "</tr>"+
                            "<tr>" +
                            "<td>"+
                             "<span i18n_label=\"manufacturer\"></span>:</br>" +
                                element.manufacturer +
                            "</td>"+
                            "<td>" +
                            "<span i18n_label=\"price.total\"></span>: </br>" +
                                (element.historicPrice * element.quantity) +
                            "</td>" +
                            "<td>" +
                            "<span i18n_label=\"shipped.on\"></span>:</br>" +
                                shippingDate +
                            "</td>" +
                            "</tr>" +
                            "</table>" +
                            "</div>" +
                            "</div>";
                    });
                    $("#items-area").html(itemsHtml);
                }).done(function (data) {
                    languageUtils.applyLabelsToHTML(utils.initiateHeaderToolTips);
                });
            }
        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-th-list"></span>  <span i18n_label="my.orders.heading"></span>
                    <div class="dropdown orders-state-filter">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span i18n_label="show.orders.in.state"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#" class="show-active"><span i18n_label="orders.active"></span></a></li>
                            <li><a href="#" class="show-ended"><span i18n_label="orders.ended"></span></a></li>
                            <li><a href="#" class="show-all"><span i18n_label="orders.all"></span></a></li>
                        </ul>
                    </div>
                </h1>
                <div id="result-panel" class="result-panel-sml"> </div>
                <div id="items-area">
             </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>