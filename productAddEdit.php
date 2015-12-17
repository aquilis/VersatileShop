<?php
include 'lib/acc_functions.php';
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
                $('.file-picker').attr("title", "Only files from your server's /images directory will be considered valid");
                $('#video-url-field').attr("title", "Paste here the 'src' attribute generated when going to a youtube video -> embed");
                $('[data-toggle="tooltip"]').tooltip();

                //FIXME: Remake the logic for generating these or move it to a common palce
                $("#thumbnail-picker").change(function(){
                    var fullPath = $("#thumbnail-picker").val();
                    var splitted = fullPath.split("\\");
                    var imageName = splitted[splitted.length-1];
                    $("#thumb-path-field").val("images/" + imageName);
                 });
                $("#pic-one-picker").change(function(){
                    var fullPath = $("#pic-one-picker").val();
                    var splitted = fullPath.split("\\");
                    var imageName = splitted[splitted.length-1];
                    $("#pic-one-field").val("images/" + imageName);
                 });
                $("#pic-two-picker").change(function(){
                    var fullPath = $("#pic-two-picker").val();
                    var splitted = fullPath.split("\\");
                    var imageName = splitted[splitted.length-1];
                    $("#pic-two-field").val("images/" + imageName);
                 });
                $("#pic-three-picker").change(function(){
                    var fullPath = $("#pic-three-picker").val();
                    var splitted = fullPath.split("\\");
                    var imageName = splitted[splitted.length-1];
                    $("#pic-three-field").val("images/" + imageName);
                 });

                $.getJSON("services/ProductsService.php?action=allCategories", function (data) {
                    var html = "";
                    $(data).each(function (index, element) {
                        html+= "<option value='"+ element.categoryID +"'>"+ element.categoryName +"</option>";
                    });
                    $("#categories").html(html);
                });

               $("#submit-btn").click(function () {
                    var productDetails = {
                        categoryID: $("#categories").val(),
                        title: $("#title-field").val(),
                        description: $("#description-field").val(),
                        price: $("#price-field").val(),
                        manufacturer: $("#manufacturer-field").val(),
                        thumbnailPath: $("#thumb-path-field").val(),
                    };

                    if($("#pic-one-field").val().length > 0) {
                        productDetails.pictureOne = $("#pic-one-field").val();
                    }
                    if($("#pic-two-field").val().length > 0) {
                        productDetails.pictureTwo = $("#pic-two-field").val();
                    }
                    if($("#pic-three-field").val().length > 0) {
                        productDetails.pictureThree = $("#pic-three-field").val();
                    }
                    if(($("#video-caption-field").val().length > 0) && ($("#video-url-field").val().length > 0)) {
                        productDetails.videoCaption = $("#video-caption-field").val();
                        productDetails.videoURL = $("#video-url-field").val();
                    }
                    //ar validationResult = validateRegistration(userDetails);
                    // if (validationResult.status === false) {
                    //     highlightErrors(validationResult);
                    //     return;
                    // }
                    //if the front-end validation passes, make the post request to the server
                    $.post("services/ProductsService.php?action=create", productDetails).done(function (data) {
                        var serverValidationResult = data;
                        if (serverValidationResult.status === false) {
                            //highlightErrors(serverValidationResult);
                            return;
                        }
                        //display the successful server response
                        var html = "<div class=\"alert alert-success\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\"></span>" +
                                "Product data added! You will be now redirected to the shop <a href='shop.php'>Shop.</a></div>";
                        var resultPanel = $("#result-panel");
                        resultPanel.fadeIn("fast");
                        resultPanel.html(html);
                        window.scrollTo(0,0);
                        resultPanel.delay(4000).fadeOut(1200, function () {
                            window.location.href = utils.getBaseURL() + "/shop.php";
                        });
                    });
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
                <h1><span class="glyphicon glyphicon-barcode"></span>  Add a new product</h1>
                <p>Fields with * are mandatory</p>
                <div class="panel panel-primary authentication-panel">
                    <div id="result-panel"></div>
                    <div class="form-group login-form">
                        <div class="mandatory-details">
                            <label>Category*</label>
                            <select id="categories" class="form-control">
                            <!-- Filled with all retrieved categories -->
                            </select><br>
                            <label>Title*</label>
                            <input id="title-field" class="form-control" type="text" placeholder="Title"><br>
                            <label>Description*</label>
                            <textarea id="description-field" class="form-control" cols="64" rows="8"></textarea>
                            <label>Price*</label>
                            <input id="price-field" class="form-control" type="price" placeholder="Price in $">
                            <label>Manufacturer*</label>
                            <input id="manufacturer-field" class="form-control" type="price" placeholder="Manufacturer/Author">
                            <label>Thumbnail path*</label>
                            <input id="thumb-path-field"  class="form-control" disabled="disabled" type="price">
                            <input id="thumbnail-picker" type="file" class="file-picker" data-toggle="tooltip" data-placement="right">
                        </div>
                        <hr>
                        <div class="additional-details">
                            <label>Product picture #1</label>
                            <input id="pic-one-field"  class="form-control" disabled="disabled" type="text">
                            <input id="pic-one-picker" type="file" class="file-picker" data-toggle="tooltip" data-placement="right"><br>
                            <label>Product picture #2</label>
                            <input id="pic-two-field"  class="form-control" disabled="disabled" type="text">
                            <input id="pic-two-picker" type="file" class="file-picker" data-toggle="tooltip" data-placement="right"><br>
                            <label>Product picture #3</label>
                            <input id="pic-three-field"  class="form-control" disabled="disabled" type="text">
                            <input id="pic-three-picker" type="file" class="file-picker" data-toggle="tooltip" data-placement="right">
                            <hr>
                            <label>Video Caption</label>
                            <input id="video-caption-field" class="form-control" type="text" placeholder="video caption"><br>
                            <label>Video embed src (via YouTube)</label>
                            <input id="video-url-field" class="form-control" type="text" 
                                    placeholder="embed URL" data-toggle="tooltip" data-placement="right"><br>
                        </div>
                        <button id="submit-btn" type="button" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once("templates/footer.php"); ?>
    </body>

</html>