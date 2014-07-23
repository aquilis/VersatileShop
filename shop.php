<?php
include 'lib/acc_functions.php'; 
?>
<html>
<head>
<title>Shop</title>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/utils.js"></script>
<script>

$(document).ready(function() {
	utils.displayAjaxLoader("items-area", "Loading...", false);
	//if the user accesses the shop page with a product ID in the URL, load the requested product.
	//Otherwise, load all available products
	if((utils.isURLParameterized(document.URL)) && (utils.isURLParameterPresent("productID"))) {
		var id = utils.getURLParameter("productID");
		loadProduct(id);
	} else {
		$(".nav li:contains('Shop')").addClass("active");
		displayAllProducts();
	}
});

/**
* Displays all available products in a grid.
**/
function displayAllProducts() {
	$.getJSON("models/shop-model.php", function(data) {
			var itemsHtml = "";
			$(data).each(function(index, element) {
    				itemsHtml +=
				   	 		  "<div class=\"col-md-3 product-tile\">" +
				   	 		  "<a style='cursor: pointer; text-decoration: none;' onclick='loadProduct("+element.productID+");'>" +
			    			  "<div class=\"thumbnail\">" + 
			      			  "<img src=\""+ element.thumbnailPath +"\">"+
			      			  "<div class=\"caption\">" + 
			        		  "<h3>" + element.title + "</h3>" + 
			        		  "<p>"+ element.description +"...</p>" + 
			        		  "<p><b>Price: "+ element.price +"$</b></p>" + 
			        		  "<p><button productID='"+element.productID+"' class='btn btn-primary'>See more...</button></p>" + 
			      			  "</div>" + 
			    			  "</div>" + 
			    			  "</a>" +
			  				  "</div>"; 

			});
			$("#items-area").html(itemsHtml);
	}).done(function() {
		$(".product-tile .btn-primary").click(function() {
			loadProduct($(this).attr("productID"));
	  })
	})
}

/**
* Loads in the page the data for the product with the given ID.
*/
function loadProduct(productID) {
		$("body").removeClass("paper-textured");
		$("body").addClass("carbon-textured");
		utils.displayAjaxLoader("contentArea", "Loading...", false);
		$.getJSON("models/shop-model.php?productID=" + productID, function(data) {
			var html =  "<div class='jumbotron white-textured soft-frame'>"+ 
					    "<h1 style='text-align: center'>"+ data.title +"</h1>"+
					    constructImageCarousel(data.images) +
					    constructVideosGrid(data.videos) +
					    "<p class=\"product-section\"><b>Added on:</b></br>"+ data.dateAdded +"</p>"+ 
					    "<p class=\"product-section\"><b>Developer:</b></br>"+ data.manufacturer +"</p>"+ 
						"<p class=\"product-section\"><b>Description:</b></br>"+ data.description +"</p>"+ 
						"<p class=\"product-section\"><b>Price:</b>"+ data.price +"$</p>"+
						"<p class=\"product-section\"><button id=\"add-to-cart-btn\"productID='"+ data.productID +"' class='btn-lg btn-primary'><span class='glyphicon glyphicon-shopping-cart'></span>Add to cart</button>"+
						"<span style='margin-left: 18px'>Quantity:  </span>" +
						"<select id=\"quantity-picker\" class=\"form-control picker-sml\">" + 
						"<option>1</option>" +
						"<option>2</option>" +
						"<option>3</option>" +
						"<option>4</option>" +
						"<option>5</option>" +
						"</select>" +
						"</p>" + 
						"<div id=\"shopping-cart-result\" class=\"result-panel-sml\"></div>"+
						"</div>";
			$("#contentArea").html(html);
			//change the browser URL so that the product can be bookmarked
			utils.changeBrowserURL(data.title, utils.getPureURL() + "?productID=" + productID);
			//remove the highlinig of the shop navigation button
			$(".nav li:contains('Shop')").removeClass("active");
			}).done(function() {
				//TODO: attach button handlers here
				$("#back-to-shop").click(function() {
					//displayAllProducts();
				});

				$("#add-to-cart-btn").click(function() {
					var id = $(this).attr("productID");
					var quantity = $("#quantity-picker").find(":selected").text();
					addToCart(id, quantity);
				});
			})
}

/**
* Constructs the images carousel for the product using the given array of image paths.
**/
function constructImageCarousel(imagesArray) {
	if(imagesArray.length<1) {
		return "";
	}
	var html= "<div class=\"carousel-holder\">"+
			  "<div id=\"product-images-carousel\" class=\"product-section carousel slide\" data-ride=\"carousel\">" + 
			  "<ol class=\"carousel-indicators\">" + 
			  "<li data-target=\"#product-images-carousel\" data-slide-to=\"0\" class=\"active\"></li>";

			  for(var i=1; i<imagesArray.length; i++) {
			  	html+= "<li data-target=\"#product-images-carousel\" data-slide-to=\""+ i +"\"></li>";
			  }
			  html+="</ol>" + 
			  "<div class=\"carousel-inner\">"+
			  "<div class=\"item active\">"+
			  "<img src='"+ imagesArray[0].imagePath +"' alt=\"product image\">"+
			  "<div class=\"carousel-caption\">"+
    		  "<h3>"+imagesArray[0].imageDescription+"</h3>"+
    		  "</div>"+
			  "</div>";

			  for(var i=1; i<imagesArray.length; i++) {
			  		html+= "<div class=\"item\">"+
						   "<img src='" + imagesArray[i].imagePath +"' alt=\"product image\">"+
						   "<div class=\"carousel-caption\">"+
    		  			   "<h3>"+imagesArray[i].imageDescription+"</h3>"+
    		 			   "</div>"+
						   "</div>";
			  }

			  html+= "</div>"+
			  "<a class=\"left carousel-control\" href=\"#product-images-carousel\" data-slide=\"prev\">"+
			  "<span class=\"glyphicon glyphicon-chevron-left\"></span>"+
			  "</a>" + 
			  "<a class=\"right carousel-control\" href=\"#product-images-carousel\" data-slide=\"next\">"+
			  "<span class=\"glyphicon glyphicon-chevron-right\"></span>"+
			  "</a>"+
			  "</div>" +
			  "</div>";
	return html;
}

/**
*	Adds the product with the given ID to the shopping cart.
**/
function addToCart(productID, quantity) {
	$.post( "models/shopping-cart-model.php", {productID: productID, quantity: quantity}, function() {
		//handlers here
	})
	 .done(function(data) {
	 	var html= "<div class=\"alert alert-success\" role=\"alert\"><span class=\"glyphicon glyphicon-ok\"></span>   "+ data +"</div>";
	    var resulPanel = $("#shopping-cart-result");
	    resulPanel.fadeIn("fast");
	    resulPanel.html(html);
	    resulPanel.delay(4000).fadeOut(1200, function() {
          		resulPanel.html("").hide();
        });
	 });
}

/**
* Generates the HTML sequence with all embedded videos for this product.
**/
function constructVideosGrid(videosArray) {
	if(videosArray.length<1) {
		return "";
	}
	var html="";
	$(videosArray).each(function(index, element) {
    				html += element.videoCaption +
    				"</br><iframe class=\"product-section\" width=\"560\" height=\"315\" src=\""+element.videoSrc+"\" frameborder=\"0\" allowfullscreen></iframe></br>";
	});
	return html;
}
</script>
</head>


<body class="paper-textured">
	<?php include_once("templates/header.php"); ?>

  <div id="mainColumn">
 	<div id="contentArea">
 		<?php include_once("templates/guest_prompt.php"); ?>
 		<?php
			if (isset($_SESSION['isAdmin'])) {
		?>
		  	<button type="button" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-plus"></span>Add product</button>
		<?php	
			}
		?>
 		<h1>Enjoy the newest games at best prices.</h1>
			<div id="items-area" class="row">			
			</div>
</div> 
</div>
<?php include_once("templates/footer.php"); ?>
</body>

</html>