<?php
include 'lib/utils.php';
?>
<html>
    <head>
    <title>Shop</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/jquery.i18n.properties.js"></script>
    <script src="js/language-utils.js"></script>
    <script>
        $(document).ready(function () {
            utils.displayAjaxLoader("items-area", "Loading...", false);
            //if the user accesses the shop page with a product ID in the URL, load the requested product.
            //Otherwise, load all available products
            if ((utils.isURLParameterized(document.URL)) && (utils.isURLParameterPresent("productID"))) {
                var id = utils.getURLParameter("productID");
                loadProduct(id);
            } else {
                $(".nav li[id=header-shop]").addClass("active");
                displayAllProducts();
            }
        });

        /**
         * Displays all available products in a bootstrap grid.
         **/
        function displayAllProducts() {
            $.getJSON("services/ShopService.php", function (data) {
                var itemsHtml = "";
                $(data).each(function (index, element) {
                    itemsHtml +=
                            "<div class=\"col-md-3 product-tile\">" +
                            "<a style='cursor: pointer; text-decoration: none;' onclick='loadProduct(" + element.productID + ");'>" +
                            "<div class=\"thumbnail\">" +
                            "<img src=\"" + element.thumbnailPath + "\">" +
                            "<div class=\"caption\">" +
                            "<h3>" + element.title + "</h3>" +
                            "<p>" + element.description + "...</p>" +
                            "<p><b><span i18n_label=\"price\"></span>: " + element.price + "$</b></p>" +
                            "<p><button productID='" + element.productID + "' class='btn btn-primary'><span i18n_label=\"see.more\"></span></button></p>" +
                            "</div>" +
                            "</div>" +
                            "</a>" +
                            "</div>";

                });
                $("#items-area").html(itemsHtml);
                languageUtils.applyLabelsToHTML(utils.initiateHeaderToolTips);
            }).done(function () {
                $(".product-tile .btn-primary").click(function () {
                    loadProduct($(this).attr("productID"));
                })
            })
        }

        /**
        * Loads the data for the product with the given ID, displays it in the page and attaches 
        * handlers to the dedicated buttons for this product.
        *
        * @param  productID
        *                   is the ID of the prodcut to load
        */
        function loadProduct(productID) {
            $("body").removeClass("paper-textured");
            $("body").addClass("carbon-textured");
            utils.displayAjaxLoader("contentArea", "Loading...", false);
            $.getJSON("services/ShopService.php?productID=" + productID, function (data) {
                var html = "";
                if (data.length === 0) {
                    html = "<img src='images/404.png' alt='Oops! No such page'></img>";
                } else {
                    html = "<div class='jumbotron white-textured soft-frame'>" +
                            "<h1 style='text-align: center'>" + data.title + "</h1>" +
                            constructImageCarousel(data.images) +
                            constructVideosGrid(data.videos) +
                            "<p class=\"product-section\"><b><span i18n_label=\"added.on\"></span>:</b></br>" + data.dateAdded + "</p>" +
                            "<p class=\"product-section\"><b><span i18n_label=\"manufacturer\"></span>:</b></br>" + data.manufacturer + "</p>" +
                            "<p class=\"product-section\"><b><span i18n_label=\"description\"></span>:</b></br>" + data.description + "</p>" +
                            "<p class=\"product-section\"><b><span i18n_label=\"quantity.in.stock\"></span>:</b></br>" + data.quantityInStock + "</p>" +
                            "<p class=\"product-section\"><b><span i18n_label=\"price\"></span>:</b>" + data.price + "$</p>" +
                            "<p class=\"product-section\"><button id=\"add-to-cart-btn\"productID='" + data.productID +
                                "' class='btn-lg btn-primary'><span class='glyphicon glyphicon-shopping-cart'></span><span i18n_label=\"add.to.cart\"></span></button>" +
                            "<span style='margin-left: 18px'><span i18n_label=\"quantity\"></span>:  </span>" +
                            "<select id=\"quantity-picker\" class=\"form-control picker-sml\">" +
                            "<option>1</option>" +
                            "<option>2</option>" +
                            "<option>3</option>" +
                            "<option>4</option>" +
                            "<option>5</option>" +
                            "</select>" +
                            "</p>" +
                            "<div id=\"shopping-cart-result\" class=\"result-panel-sml\"></div>" +
                            "</div>";
                }
                $("#contentArea").html(html);
                //change the browser URL so that the product can be bookmarked
                utils.changeBrowserURL(data.title, utils.getPureURL() + "?productID=" + productID);
                //remove the highlinig of the shop navigation button
                $(".nav li:contains('Shop')").removeClass("active");
            }).done(function () {
                $("#back-to-shop").click(function () {
                    //displayAllProducts();
                });

                $("#add-to-cart-btn").click(function () {
                    var id = $(this).attr("productID");
                    var quantity = $("#quantity-picker").find(":selected").text();
                    addToCart(id, quantity);
                });
                languageUtils.applyLabelsToHTML(utils.initiateHeaderToolTips);
            });
        }

        /**
         * Constructs a bootstrap carousel of products using the given array of image objects.
         * 
         * @param imagesArray
         *                  is an array of image objects where each has a source path and description
         * 
         * @returns  
         *          the html of the generated image carousel
         * */
        function constructImageCarousel(imagesArray) {
            if (imagesArray.length === 0) {
                return "";
            }
            var html = "<div class=\"carousel-holder\">" +
                    "<div id=\"product-images-carousel\" class=\"product-section carousel slide\" data-ride=\"carousel\">" +
                    "<ol class=\"carousel-indicators\">" +
                    "<li data-target=\"#product-images-carousel\" data-slide-to=\"0\" class=\"active\"></li>";

            for (var i = 1; i < imagesArray.length; i++) {
                html += "<li data-target=\"#product-images-carousel\" data-slide-to=\"" + i + "\"></li>";
            }
            html += "</ol>" +
                    "<div class=\"carousel-inner\">" +
                    "<div class=\"item active\">" +
                    "<img src='" + imagesArray[0].imagePath + "' alt=\"product image\">" +
                    "<div class=\"carousel-caption\">" +
                    "<h3>" + imagesArray[0].imageDescription + "</h3>" +
                    "</div>" +
                    "</div>";

            for (var i = 1; i < imagesArray.length; i++) {
                html += "<div class=\"item\">" +
                        "<img src='" + imagesArray[i].imagePath + "' alt=\"product image\">" +
                        "<div class=\"carousel-caption\">" +
                        "<h3>" + imagesArray[i].imageDescription + "</h3>" +
                        "</div>" +
                        "</div>";
            }

            html += "</div>" +
                "<a class=\"left carousel-control\" href=\"#product-images-carousel\" data-slide=\"prev\">" +
                "<span class=\"glyphicon glyphicon-chevron-left\"></span>" +
                "</a>" +
                "<a class=\"right carousel-control\" href=\"#product-images-carousel\" data-slide=\"next\">" +
                "<span class=\"glyphicon glyphicon-chevron-right\"></span>" +
                "</a>" +
                "</div>" +
                "</div>";
            return html;
        }

        /**
         * Adds the product with the given ID to the shopping cart and displays an ajax result dialog.
         * 
         * @param productID
         *                  is the ID of the product to be aded to the cart
         * @param quantity
         *                  is the quantity for the product
         */
        function addToCart(productID, quantity) {
            $.post("services/ShoppingCartService.php", {productID: productID, quantity: quantity}, function () {
            }).done(function (data) {
                    utils.initiateHeaderToolTips();
                    var html = "<div class=\"alert alert-success\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\"></span>   " + jQuery.i18n.map[data.trim()] + "</div>";
                    var resulPanel = $("#shopping-cart-result");
                    resulPanel.fadeIn("fast");
                    resulPanel.html(html);
                    resulPanel.delay(4000).fadeOut(1200, function () {
                        resulPanel.html("").hide();
                    });
                });
            }

        /**
         * Generates a HTML sequence of embedded videos, provided an array of video objects.
         * 
         * @param videosArray
         *                      is an array of video objects where each has a caption and video source
         * @returns 
         *          the html of the generated videos sequence        
         */
        function constructVideosGrid(videosArray) {
            if (videosArray.length === 0) {
                return "";
            }
            var html = "";
            $(videosArray).each(function (index, element) {
                html += element.videoCaption +
                        "</br><iframe class=\"product-section youtube-video\"  src=\"" + element.videoSrc + "\" frameborder=\"0\" allowfullscreen></iframe></br>";
            });
            return html;
        }
    </script>
</head>


<body class="paper-textured">
    <?php include_once("templates/header.php"); ?>

    <div id="mainColumn">
        <div id="contentArea">
            <?php
            if (isset($_SESSION['isAdmin'])) {
                ?>
                <a href="productAddEdit.php"><button type="button" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-plus"></span>Add product</button></a>
                <a href="HistoryOfOrders.php"><button type="button" class="btn btn-lg btn-info">History of orders</button></a>
                <?php
            }
            ?>
            <h1><span i18n_label="shop.page.caption"></span></h1>
            <div id="items-area" class="row">			
            </div>
        </div> 
    </div>
    <?php include_once("templates/footer.php"); ?>
</body>

</html>