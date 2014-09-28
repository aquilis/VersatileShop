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
<script>
$(document).ready(function(){
	//load all titles for the autocomplete and bind the autocomplete function
	$.getJSON("models/search-model.php?action=allTitles", function(data) {
		 $("#search-by-title").autocomplete({
			source: data,
			select: function(event, ui) {
				$("#search-by-title").val(ui.item.label);
				return false;
			}
		})
	});
});
</script>
</head>


<body class="paper-textured">
	<?php include_once("templates/header.php"); ?>

  <div id="mainColumn">
 	<div id="contentArea">
 		<?php include_once("templates/guest_prompt.php"); ?>

 		<h1><span class="glyphicon glyphicon-search"></span>  Search for products in the shop.</h1>
 		<div class="small-form">
 			<input id="search-by-title" type="text" class="form-control" placeholder="Search by title">
			<input type="text" class="form-control" placeholder="Search by description">
			<button type="button" class="btn btn-primary">Search</button>
 		</div>
		<div id="items-area" class="row">	
		</div>
</div> 
</div>
<?php include_once("templates/footer.php"); ?>
</body>

</html>