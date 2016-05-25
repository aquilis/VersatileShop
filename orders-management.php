<?php
include 'lib/utils.php';
if(!isLogged() || !isset($_SESSION['isAdmin'])) {
    header("Location: login.php");
    die();
}
?>
<html>
    <head>
        <title>Orders management</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/select2.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/language-utils.js"></script>
        <script src="js/select2.js"></script>
        <script>
            var ORDERS_ACTIVE = "active";
            var ORDERS_ENDED = "ended";
            var ORDERS_ALL = "all";

            var STATUS_PENDING = "order.state.pending";
            var STATUS_SHIPPED =  "order.state.shipped";
            var STATUS_REJECTED =  "order.state.rejected";
            var STATUS_RECEIVED = "order.state.received";

            var TIME_INTERVAL_DAY = "time.interval.day";
            var TIME_INTERVAL_WEEK = "time.interval.week";
            var TIME_INTERVAL_MONTH = "time.interval.month";

            //indicates if the i18n labels have already been initalized after the page has been loaded
            var intializedLabels = false;

            $(document).ready(function () {
                $(".nav li[id=header-orders-management]").addClass("active");
                doSearch();

                $("#search-btn").click(function() {
                    doSearch();
                });

                $("#reset-btn").click(function() {
                    $("#orders-search-form").find("select").select2("val", "");
                    $("#orders-search-form").find("input").val("");
                });
            });

            /**
             * Generates the inner HTML of the status-picker combo box with its predefined options
             **/
            function generateStatusOptions() {
                var html= "<option value=\"\"></option>" +
                         " <option value=\"" + STATUS_PENDING + "\">" + jQuery.i18n.map[STATUS_PENDING] + "</option>" +
                         " <option value=\"" + STATUS_SHIPPED + "\">" + jQuery.i18n.map[STATUS_SHIPPED] + "</option>" +
                         " <option value=\"" + STATUS_RECEIVED + "\">" + jQuery.i18n.map[STATUS_RECEIVED] + "</option>" +
                         " <option value=\"" + STATUS_REJECTED + "\">" + jQuery.i18n.map[STATUS_REJECTED] + "</option>";
                $(".status-picker").html(html);
                $(".status-picker").select2();
            }

            /**
             * Generates the inner HTML of the time interval-picker combo box with its predefined options
             **/
            function generateTimeIntervalOptions() {
                var html= "<option value=\"\"></option>" +
                    " <option value=\"" + TIME_INTERVAL_DAY + "\">" + jQuery.i18n.map[TIME_INTERVAL_DAY] + "</option>" +
                    " <option value=\"" + TIME_INTERVAL_WEEK + "\">" + jQuery.i18n.map[TIME_INTERVAL_WEEK] + "</option>" +
                    " <option value=\"" + TIME_INTERVAL_MONTH + "\">" + jQuery.i18n.map[TIME_INTERVAL_MONTH] + "</option>";
                $(".time-interval-picker").html(html);
                $(".time-interval-picker").select2();
            }

            /**
             * Extracts the search arguments from the form and initiates the search.
             **/
            function doSearch() {
                var queryParams = "";
                var status = $(".status-picker").val();
                var dateRange = $(".time-interval-picker").val();
                var username =  $(".username-picker").val();
                if(status) {
                    queryParams+= "&status=" + status;
                }
                if(dateRange) {
                    queryParams+= "&dateRange=" + dateRange;
                }
                if(username) {
                    queryParams+= "&username=" + username;
                }
                loadOrdersByCriteria(queryParams);
            }

            /**
             * Loads the orders, according to the passed query parameters criteria string.
             *
             * @param queryParams
             *                   is the query params string to be passed with the GET request
             */
            function loadOrdersByCriteria(queryParams) {
                utils.displayAjaxLoader("items-area", "Loading...", false);
                $.getJSON("services/OrdersService.php?action=ordersByCriteria" + queryParams, function (data) {
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
                                utils.parseDate(new Date(element.orderDate)) +
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
                    languageUtils.applyLabelsToHTML(function() {
                        //these have to be initialized only once per page load :)
                        if(!intializedLabels) {
                            generateStatusOptions();
                            generateTimeIntervalOptions();
                            var config = utils.getBasicAutocompleteConfig("services/UsersService.php?action=getAllUsernames", "username", "username");
                            $(".username-picker").select2(config);
                            intializedLabels = true;
                        }
                        utils.initializeHeaderBehaviour();
                    });
                });
            }
        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-globe"></span>  <span i18n_label="orders.management.heading"></span></h1>
                <div id="orders-search-form" class="search-form-compact">
                    <div class="row">
                        <div class="col-md-4">
                            <span i18n_label="order.state"></span>
                            <select class="status-picker"></select>
                        </div>
                        <div class="col-md-4">
                            <span i18n_label="ordered.during.last"></span>
                            <select class="time-interval-picker"></select>
                        </div>
                        <div class="col-md-4">
                            <span i18n_label="ordered.by.username"></span>
                            <select class="username-picker"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <button id="search-btn" type="submit" class="btn btn-primary"><span i18n_label="search.page.search"></span></button>
                            <button id="reset-btn" type="submit" class="btn btn-default"><span i18n_label="search.page.reset"></span></button>
                        </div>
                    </div>
                </div>
                <div id="result-panel" class="result-panel-sml"> </div>
                <div id="items-area">
                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>