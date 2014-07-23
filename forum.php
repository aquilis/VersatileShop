<?php
include "lib/acc_functions.php"; 
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
          	$(".nav li:contains('Talk')").addClass("active");
          	utils.displayAjaxLoader("entries-holder", "Loading...", false);
          	listForumEntries(1);
          	displayPager();
          });

		  /**
		  * Waits for a certain amount of time and then fades out and hides the results panel
		  **/
          function fadeResultPanel() {
          	$(".result-panel .alert").delay(2000).fadeOut(1200, function() {
          		$(".result-panel").html("").hide();
          	});
          }

		  /**
		  * Asynchronously makes a GET request to the server and displays all forum entries according to the given page number
		  **/
          function listForumEntries(pageNumber) {
          	$.getJSON("models/forum-model.php?page=" + pageNumber, function(data) {
	          	var html = "";
				$(data).each(function(index, element) {
	    			html +=
					"<div class=\"panel panel-default\">" + 
		  			"<div class=\"panel-heading\"><b>"+ element.author+ "</b>"+ element.badges +" said on <i>"+ 
		  			element.date +"</i>" +
		  			<?php
			  			if (isset($_SESSION['isAdmin'])) {
			  		?>
		  			"<a author='"+element.author+"' date='"+element.date+"'><span style='float: right;  cursor: pointer;' class='glyphicon glyphicon-trash'> Delete</span></a>" +
		  			<?php	
			  			}
		  			?>
		  			"</div>" + 
		  			"<div class=\"panel-body\">" + 
		    		element.content +
		  			"</div>" + 
					"</div>"; 
				});
				$("#entries-holder").html(html);
					<?php
			  			if (isset($_SESSION['isAdmin'])) {
			  		?>
			  				//attach a handler to the delete button to delete the given post
			  				$("#entries-holder").find(".panel-heading a").click(function() {
			  					utils.displayAjaxLoader("entries-holder", "Loading...", false);
					          	$.ajax({
					    			url: "models/forum-model.php",
					    			type: "POST",
					    			dataType: "json",
					    			data: {action: "delete", delName: $(this).attr("author"), delDate: $(this).attr("date")}
								})
								.done(function(data) {
									var holder = $(".result-panel");
									holder.show();
									if(data.status == "ok") {
										holder.html("<div class=\"alert alert-success\">" + data.message + "</div>");
									} else {
										holder.html("<div class=\"alert alert-danger\">" + data.message + "</div>");
									}
									fadeResultPanel();
								});	
								listForumEntries(1);
								displayPager();
			  				})
			  		<?php	
			  			}
		  			?>
				})
          }

		  /**
		  * Asynchronously makes a POST request to the server and saves into the DB the post form's content from the currently logged user
		  **/
          function postEntry() {
          	utils.displayAjaxLoader("entries-holder", "Loading...", false);
          	var textArea = $('textarea#contentArea');
          	var contentText = textArea.val();
          	textArea.val("");
          	$.ajax({
				url: "models/forum-model.php",
				type: "POST",
				data: {action: "post", content: contentText}
			})
			.done(function(data) {
				var holder = $(".result-panel");
				holder.show();
				if(data.status == "ok") {
					holder.html("<div class=\"alert alert-success\">" + data.message + "</div>");
				} else {
					holder.html("<div class=\"alert alert-danger\">" + data.message + "</div>");
				}
				fadeResultPanel();
				listForumEntries(1);
				displayPager();
			}).fail(function() {
				console.log("The post request failed");
			});
         }

		  /**
		  * Asynchronously gets from the sevrer the needed data and displays the pagination buttons
		  **/
         function displayPager() {
	         $.getJSON("models/forum-model.php?action=pagingData", function(data) {
	         	var total = data.numberOfPosts;
	         	var postsPerPage = data.postsPerPage;
	         	var html = "<ul class=\"pagination\">" + 
	         			   "<li class=\"active\"><a href=\"#\">1</a></li>";
	         	for(var i=1; i<total/postsPerPage; i++) {
	         		html+= "<li><a href=\"#\">"+ (i+1) +"</a></li>";
	         	}
	         	html+= "</ul>";
	         	$("#pager").html(html);
	         	$("#pager ul li").click(function() {
	         		utils.displayAjaxLoader("entries-holder", "Loading...", false);
	         		$("#pager ul").find(".active").removeClass("active");
	         		$(this).addClass("active");
	         		listForumEntries($(this).find("a").html());
	         	});
	     	});
         }
    </script>
</head>

<body class="paper-textured">
	<?php include_once("templates/header.php"); ?>

<div id="mainColumn">
<div id="contentArea">
 		<?php 
 			include_once("templates/guest_prompt.php"); 
 			include_once("templates/post-form.php");
 		?>
 		<div id="result" class="result-panel">
 		</div>

 		<div id="entries-holder" class="forum-entries-holder">
 		</div>

 		<div id="pager">
 		</div>
</div>
</div>
		<?php include_once("templates/footer.php"); ?>
</body>

</html>