<?php
include 'lib/utils.php';
if(!isLogged() || !isset($_SESSION['isAdmin'])) {
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
        <script src="js/jquery.flot.navigate.js"></script>
        <script src="js/jquery.flot.pie.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/language-utils.js"></script>
        <script>


            var TIME_INTERVAL_DAY = "day";
            var TIME_INTERVAL_MONTH = "month";
            var TIME_INTERVAL_YEAR = "year";

            $(document).ready(function () {
                $(".nav li[id=header-dashboard]").addClass("active");
                languageUtils.applyLabelsToHTML(utils.initializeHeaderBehaviour);
                loadOrdersInTimeWidget(TIME_INTERVAL_DAY);
                loadMostBoughtProductWidget();
                loadRevenueByTimeWidget(TIME_INTERVAL_DAY);
                loadSuppliesByProductWidget();
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

            /**
             * Plots a pie chart in the given element, according to the passed params.
             *
             * @param widgetElement is the parent element where to draw it
             * @param statisticsType is the statistic type that will be requested from the serber
             * @param labelAttributeName is the attribute name that will be taken as labels for the pie sections
             * @param dataAttributeName is the attribute name that will be taken as data for the pie sections
             */
            function plotPieChart(widgetElement, statisticsType, labelAttributeName, dataAttributeName) {
                $.getJSON("services/StatisticsService.php?statisticsType=" + statisticsType, function (data) {
                    widgetElement.html('');
                    var chartData = [];
                    $(data).each(function (index, element) {
                        chartData.push({
                            label: element[labelAttributeName],
                            data: element[dataAttributeName]
                        });
                    });
                    $.plot("#" + widgetElement.attr('id'), chartData, {
                        series: {
                            pie: {
                                show: true,
                                label: {
                                    show: true
                                }
                            }
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        legend: {
                            show: false
                        }
                    });
                });
            };

            function loadSuppliesByProductWidget() {
                var widgetElement = $("#supplies-by-product");
                widgetElement.html('<img id=\"ajax-loader\" src=\"images/ajax-loader.gif\"><br> Loading... </img><br>');
                plotPieChart(widgetElement, "supplies-by-product",  "title", "suppliesCount");
            };

            function loadMostBoughtProductWidget() {
                var widgetElement =  $("#most-bought-product");
                $("#most-bought-product").html('<img id=\"ajax-loader\" src=\"images/ajax-loader.gif\"><br> Loading... </img><br>');
                plotPieChart(widgetElement, "most-bought-products",  "title", "productOrdersCount");
            };

            function loadRevenueByTimeWidget(timePeriod) {
                $(".revenue-by-time-period-picker #time-period-date").click(function() {
                    loadRevenueByTimeWidget(TIME_INTERVAL_DAY);
                });
                $(".revenue-by-time-period-picker #time-period-month").click(function() {
                    loadRevenueByTimeWidget(TIME_INTERVAL_MONTH);
                });
                $(".revenue-by-time-period-picker #time-period-year").click(function() {
                    loadRevenueByTimeWidget(TIME_INTERVAL_YEAR);
                });
                $("#revenue-by-time").html('<img id=\"ajax-loader\" src=\"images/ajax-loader.gif\"><br> Loading... </img><br>');
                $.getJSON("services/StatisticsService.php?statisticsType=revenue-by-time&period="+ timePeriod , function (data) {
                    $("#revenue-by-time").html('');
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
                        chartData.push([evaluateDateInternal(attribute, timePeriod), parseInt(element.revenue)]);
                    });
                    $.plot("#revenue-by-time", [chartData], {
                        series: {
                            bars: {
                                show: true,
                                align: "center"
                            }
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
                            panRange: false,
                            minTickSize: 1,
                            tickDecimals: 0
                        },
                        zoom: {
                            interactive: true
                        },
                        pan: {
                            interactive: true
                        }
                    });
                });
            };

            function loadOrdersInTimeWidget(timePeriod) {
                $(".orders-by-time-period-picker #time-period-date").click(function() {
                    loadOrdersInTimeWidget(TIME_INTERVAL_DAY);
                });
                $(".orders-by-time-period-picker #time-period-month").click(function() {
                    loadOrdersInTimeWidget(TIME_INTERVAL_MONTH);
                });
                $(".orders-by-time-period-picker #time-period-year").click(function() {
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
                        },
                        zoom: {
                            interactive: true
                        },
                        pan: {
                            interactive: true
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
                            <div class="dashboard-widget-wrapper">
                                <h3 style="display: inline"><span i18n_label="orders.in.time"></span></h3>
                                <div style= "display: inline" class="dropdown orders-by-time-period-picker">
                                    <button class="btn btn-default dropdown-toggle" style="display: inline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span i18n_label="group.by"></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                        <li><a id="time-period-date"><span i18n_label="time.interval.date"></span></a></li>
                                        <li><a id="time-period-month"><span i18n_label="time.interval.month"></span></a></li>
                                        <li><a id="time-period-year"><span i18n_label="time.interval.year"></span></a></li>
                                    </ul>
                                </div>
                                <div id="orders-in-time" class="widget-placeholder">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-widget-wrapper">
                                <h3><span i18n_label="market.share.by.product"></span></h3>
                                <div id="most-bought-product" class="widget-placeholder">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-widget-wrapper">
                                <h3 style="display: inline"><span i18n_label="revenue.by.time"></span></h3>
                                <div style= "display: inline" class="dropdown revenue-by-time-period-picker">
                                    <button class="btn btn-default dropdown-toggle" style="display: inline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span i18n_label="group.by"></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                        <li><a id="time-period-date"><span i18n_label="time.interval.date"></span></a></li>
                                        <li><a id="time-period-month"><span i18n_label="time.interval.month"></span></a></li>
                                        <li><a id="time-period-year"><span i18n_label="time.interval.year"></span></a></li>
                                    </ul>
                                </div>
                                <div id="revenue-by-time" class="widget-placeholder">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-widget-wrapper">
                                <h3><span i18n_label="supplies.by.product"></span></h3>
                                <div id="supplies-by-product" class="widget-placeholder">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>