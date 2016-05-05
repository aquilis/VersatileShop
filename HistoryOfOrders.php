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
        <script>
            $(document).ready(function () {

                $("#search-btn").click(function () {
                    var username = $("#username-field").val();
                    if(username.length > 0) {
                         $.getJSON("services/OrdersService.php?username="+ username , function (data) {

                            var lastOrderID = -1;
                            var printOrderInfo = true;
                            var html = "";
                            $(data).each(function (index, element) {
                                if(element.orderID != lastOrderID) {
                                    if(lastOrderID!=-1) {
                                        html+= "</ul>";
                                    }
                                    //print bolded
                                    printOrderInfo = true;
                                    lastOrderID = element.orderID;

                                } else {
                                    printOrderInfo = false;
                                }
                                
                                if(printOrderInfo) {
                                    html+="<h3> Order ID: "+ element.orderID + " | ordered on: " + element.orderDate + "</h3> <ul>"
                                }

                                html+= "<li> Title: " + element.title + " | Quantity: " + element.quantity + "</li>";


                                //html+= "<span> " + element.orderID + "|" + element.orderDate + "|" + element.title + "|" + element.quantity + " </span>";
                            });
                            $("#search-results").html(html);
                        });
                    }
                   
                });
           });
        </script>
    </head>

    <body class="paper-textured"> 
        <?php include_once("templates/header.php"); ?>
        <?php
            if (!isset($_SESSION['isAdmin'])) {
                //redirect to a "permission denied page?"
                header("Location: index.php");
                exit();
            }
        ?>

        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-barcode"></span>  History of orders</h1>
                <div class="panel panel-primary authentication-panel">
                    <div id="result-panel"></div>
                    <div class="form-group login-form">
                        <div class="mandatory-details">
                            <label>Username*</label>
                            <input id="username-field" class="form-control" type="text" placeholder="Username"><br>
                        </div>
                        <button id="search-btn" type="button" class="btn btn-primary">Search</button>
                    </div>
                    <div id="search-results">
                    </div>
                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>