<?php
include 'lib/acc_functions.php';
?>
<html>
    <head>
        <title>Search for products</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/jquery-1.11.1-ui.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/language-utils.js"></script>
        <script>
            $(document).ready(function () {
                $(".nav li[id=header-search]").addClass("active");
                /**
                 * Retrieves and displays all products according to the given search criteria.
                 *
                 * @param criteria 
                 *		is an array of search criteria, where each element is an object containing a product property and its value. 
                 *		Example: [{property: "title", value: "some title"}, {property: "description", value: "sample description"}]
                 **/
                function displayProductsByCriteria(criteria) {
                    //first build the query params that will be attached to the URL
                    var queryParams = "";
                    $(criteria).each(function (index, element) {
                        queryParams += "&" + element.property + "=" + element.value;
                    });
                    //make the get request and display the result
                    $.getJSON("services/SearchService.php?action=search" + queryParams, function (data) {
                        var itemsHtml = "";
                        if (data.length === 0) {
                            itemsHtml = "<h4>" + jQuery.i18n.map['search.page.no.results'] + "</h4>";
                        } else {
                            $(data).each(function (index, element) {
                                var productLandingPage = utils.getBaseURL() + "/shop.php?productID=" + element.productID;
                                itemsHtml +=
                                        "<div class=\"col-md-3 product-tile\">" +
                                        "<a href='" + productLandingPage + "' style='cursor: pointer; text-decoration: none;'>" +
                                        "<div class=\"thumbnail\">" +
                                        "<img src=\"" + element.thumbnailPath + "\">" +
                                        "<div class=\"caption\">" +
                                        "<h3>" + element.title + "</h3>" +
                                        "<p>" + element.description + "...</p>" +
                                        "<p><b>" + jQuery.i18n.map['price'] + ": " + element.price + "$</b></p>" +
                                        "<p><button productID='" + element.productID + "' class='btn btn-primary'>" + jQuery.i18n.map['see.more'] + "</button></p>" +
                                        "</div>" +
                                        "</div>" +
                                        "</a>" +
                                        "</div>";

                            });
                        }
                        $("#items-area").html(itemsHtml);
                    }).done(function () {
                        $(".product-tile .btn-primary").click(function () {
                            loadProduct($(this).attr("productID"));
                        });
                    });
                }

                /**
                 *  Attaches a click handler to the search button and builds the search criteria.
                 **/
                $("#search-btn").click(function () {
                    utils.displayAjaxLoader("items-area", "Loading...", false);
                    var criteria = [];
                    if ($("#search-by-title").val().length > 0) {
                        criteria.push({property: "title", value: $("#search-by-title").val()});
                    }
                    if ($("#search-by-description").val().length > 0) {
                        criteria.push({property: "description", value: $("#search-by-description").val()});
                    }
                    displayProductsByCriteria(criteria);
                });

                /**
                 *  Loads all product titles for the autocomplete.
                 **/
                $.getJSON("services/SearchService.php?action=allTitles", function (data) {
                    $("#search-by-title").autocomplete({
                        source: data,
                        select: function (event, ui) {
                            $("#search-by-title").val(ui.item.label);
                            return false;
                        }
                    });
                });
                languageUtils.applyLabelsToHTML();
            });
        </script>
    </head>


    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>

        <div id="mainColumn">
            <div id="contentArea">
                <h1><span class="glyphicon glyphicon-search"></span>  <span i18n_label="search.page.caption"></span></h1>
                <div class="small-form">
                    <input id="search-by-title" type="text" class="form-control" placeholder="Search by title">
                    <input id="search-by-description" type="text" class="form-control" placeholder="Search by description">
                    <button id="search-btn" type="button" class="btn btn-primary"><span i18n_label="search.page.search"></span></button>
                </div>
                <div id="items-area" class="row">	
                </div>
            </div> 
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>