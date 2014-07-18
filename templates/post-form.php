<?php
if (isLogged()) {
				//echo the form for posting a new comment
				echo "
					    <div class = \"msg-post-form\">
					    <h3><img src=\"images/chat-icon.jpg\" alt=\"chat incon\"/>  Welcome to talk zone! Ask or discuss anything with fellows.</h3>
			 		    <div class=\"panel panel-primary msg-post-form\">
				        <label>Message</label>
				        <textarea id='contentArea' class=\"form-control\" rows=\"3\" name=\"content\"></textarea>
				        <br/><br/><button type=\"button\" onclick='postEntry();' class=\"btn btn-primary\">Submit</button>
						</div>
						</div>";
}
?>