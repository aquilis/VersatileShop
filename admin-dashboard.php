<?php
include 'lib/utils.php';
if(!isLogged()) {
    header("Location: login.php");
    die();
}
?>
<html>
    <head>
        <title>Admin Dasboard</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/jquery.flot.js"></script>
        <script src="js/jquery.flot.categories.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/language-utils.js"></script>
        <script>


            var TIME_INTERVAL_DAY = "day";
            var TIME_INTERVAL_MONTH = "month";
            var TIME_INTERVAL_YEAR = "year";

            $(document).ready(function () {
                $(".nav li[id=header-orders]").addClass("active");
                languageUtils.applyLabelsToHTML(utils.initiateHeaderToolTips);
                loadOrdersInTimeWidget(TIME_INTERVAL_DAY);
            });


            function evaluateDateInternal(date, timePeriod) {
                var monthNames = ["", "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                if(timePeriod === TIME_INTERVAL_DAY) {
                    return utils.parseDate(new Date(date));
                } else if (timePeriod === TIME_INTERVAL_MONTH) {
                    return monthNames[date];
                } else if (timePeriod === TIME_INTERVAL_YEAR) {
                    return date;
                }
            };


            function loadOrdersInTimeWidget(timePeriod) {
                $("#time-period-date").click(function() {
                    loadOrdersInTimeWidget(TIME_INTERVAL_DAY);
                });
                $("#time-period-month").click(function() {
                    loadOrdersInTimeWidget(TIME_INTERVAL_MONTH);
                });
                $("#time-period-year").click(function() {
                    loadOrdersInTimeWidget(TIME_INTERVAL_YEAR);
                });
                $("#orders-in-time").html('<img id=\"ajax-loader\" src=\"images/ajax-loader.gif\"><br> Loading... </img><br>');
                $.getJSON("services/StatisticsService.php?statisticsType=orders-in-time&period="+timePeriod, function (data) {
                    $("#orders-in-time").html('');
                    var chartData = [];
                    $(data).each(function (index, element) {
                        var attribute = "";
                        if(timePeriod === TIME_INTERVAL_DAY) {
                            attribute = element["orderDate"];
                        } else if (timePeriod === TIME_INTERVAL_MONTH) {
                            attribute = element["month(orders.orderDate)"];
                        } else if (timePeriod === TIME_INTERVAL_YEAR) {
                            attribute = element["year(orders.orderDate)"];
                        }
                        chartData.push([evaluateDateInternal(attribute, timePeriod), parseInt(element.ordersCount)]);
                    });
                    $.plot("#orders-in-time", [chartData], {
                        series: {
                            lines: {
                                show: true
                            },
                            points: {
                                radius: 3,
                                show: true,
                                fill: true
                            },
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        legend: {
                            labelBoxBorderColor: "none",
                            position: "right"
                        },
                        xaxis: {
                            mode: "categories",
                            tickLength: 0
                        },
                        yaxis: {
                            minTickSize: 1,
                            tickDecimals: 0
                        }
                    });
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
                <h1><span class="glyphicon glyphicon-signal"></span>  <span i18n_label="admin.dashboard.heading"></span>
                </h1>
                <div id="result-panel" class="result-panel-sml"> </div>
                <div id="items-area">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><span i18n_label="orders.in.time"></span>
                                <div style= "display: inline" class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" style="display: inline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span i18n_label="group.by"></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                        <li><a id="time-period-date" href="#"><span i18n_label="time.interval.date"></span></a></li>
                                        <li><a id="time-period-month" href="#"><span i18n_label="time.interval.month"></span></a></li>
                                        <li><a id="time-period-year" href="#"><span i18n_label="time.interval.year"></span></a></li>
                                    </ul>
                                </div></h3>

                            <div id="orders-in-time" class="widget-placeholder">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="placeholder" class="widget-placeholder">
                                <img id="ajax-loader" src="images/ajax-loader.gif"><br> Loading... </img><br>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="placeholder" class="widget-placeholder">
                                <img id="ajax-loader" src="images/ajax-loader.gif"><br> Loading... </img><br>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="placeholder" class="widget-placeholder">
                                <img id="ajax-loader" src="images/ajax-loader.gif"><br> Loading... </img><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>