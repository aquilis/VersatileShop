<?php
include 'lib/acc_functions.php'; 
?>
<html>
<head>
<title>Aquilis's dynamic web page</title>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/utils.js"></script>
<script>
$(document).ready(function() {
	$(".nav li:contains('View shopping cart')").addClass("active");
	utils.displayAjaxLoader("items-area", "Loading...", false);
	$.getJSON("models/shopping-cart-model.php", function(data) {
			var itemsHtml = "";
			var totalSum = 0;
			$(data).each(function(index, element) {
    				itemsHtml +=
    						  "<div class=\"panel panel-info\">" + 
	    						  "<div class=\"panel-heading\"><h4>"+ 
	    						  	element.title +
	    						  "</h4></div>"+
	    						  "<div class=\"panel-body\">"+ 
	    						  	  " <a href='shop.php?productID="+element.productID+"' class=\"thumbnail shopping-cart-thumbnail\">" +
      								  "<img src='"+element.thumbnailPath+"' alt=\"thumbnail\">" +
    								  "</a>"+
		    						  "<p>Quantity: " + element.quantity +
		    						  "<p>Price for a single game: " + element.price +
		    						  "<p>Price total: " + (element.price*element.quantity) +
	    						  "</div>"+
    						  "</div>";
    						  totalSum+= (element.price*element.quantity);

			});
			itemsHtml+="<h4>Total: " + totalSum + "$</h4>";
			$(".row").html(itemsHtml);
	})

	$("#empty-cart-btn").click(function() {
		utils.displayAjaxLoader("items-area", "Loading...", false);
		$.ajax({
				url: "models/shopping-cart-model.php",
				type: "DELETE",
		}).done(function() {
			$("#items-area").html("");
		})
	})
});
</script>
</head>


<body class="paper-textured">
	<?php include_once("templates/header.php"); ?>

  <div id="mainColumn">
 	<div id="contentArea">
 		<?php include_once("templates/guest_prompt.php"); ?>
 		<h1><span class="glyphicon glyphicon-shopping-cart"></span>Your shopping cart</h1>
			<div id="items-area" class="row">			
			</div>
			<button id="empty-cart-btn" class="btn btn-lg btn-warning">Empty cart</button>
			<button class="btn btn-lg btn-primary">To checkout</button>
</div> 
</div>
<?php include_once("templates/footer.php"); ?>
</body>

</html>