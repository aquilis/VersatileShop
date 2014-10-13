/**
 * A JS module containing commonly used utility functions.
 * 
 * @author V. Tsonev
 */
var utils = utils|| {};
/**
 * displays the ajax loader into the given DOM element - 'parentElementId' with
 * the given supporting text into it.
 * 
 * @param parentElementId
 *            is the ID of the parent element where the loader will be displayed
 * @param loadingPrompt
 *            is a text to be displayed under the loader
 * @param append -
 *            if set to true, the ajax loader will be appended to the content of
 *            the container, instead of clearing everything in it
 */
utils.displayAjaxLoader = function(parentElementId, loadingPrompt, append) {
	var loaderHtml = "<img id=\"ajax-loader\" src=\"images/ajax-loader.gif\"><br>"
			+ loadingPrompt + "</img><br>";
	if (append) {
		$("#" + parentElementId).append(loaderHtml);
	} else {
		$("#" + parentElementId).html(loaderHtml);
	}
}

/**
*	Asynchronously displays a result text with the given content in the given parent element and 
* 	fade it out after a certain time has passed (4 sec).
	@param speed - indicates the fade speed of the panel and can be slow, fast, normal or given in ms 
**/
utils.displayAndFadeOutResultsPanel = function(parentElementId, content, fadeSpeed) {
	var resultPanel = $("#"+ parentElementId);
	 resultPanel.fadeIn(fadeSpeed);
	 resultPanel.html(content);
	 resultPanel.delay(4000).fadeOut(1200, function() {
	    resultPanel.html("").hide();
	 });
}

/**
 * Performs the ajax GET request to the url and passes the response (as a js
 * object) to the handlerFunction
 * 
 * @param url
 *            is the service's url
 * @param handlerFunction
 *            is a function with one parameter that will handle the response (a
 *            js object)
 * 
 */
utils.makeGetJsonRequest = function(url, handlerFunction) {
	$.getJSON(url, function() {
	}).done(function(data) {
		handlerFunction(data);
	}).fail(function() {
		//REVIEW: maybe popup a CLS dialog with the error here
		console.log("There was an error sending the request to the server");
	})
}

/**
 * Resets all input text fields that are children of the given parent element.
 */
utils.resetAllInputFields = function(parentElementID) {
	$("#" + parentElementID).find(":text").val('');
}

/**
 * Modifies the browser's address bar URL asynchronously (Won't work with IE
 * version below 10).
 * 
 * @param newPageTitle
 *            is the new page title for the browser
 * @param newURL
 *            is the new URL to be set in the address bar
 */
utils.changeBrowserURL = function(newPageTitle, newURL) {
	history.pushState({}, newPageTitle, newURL);
}

/**
* Checks if a URL parameter with the given name is present in the URL.
*
**/
utils.isURLParameterPresent = function(param) {
	var url = window.location.search.substring(1);
	return (url.indexOf(param+"=") != -1);
}

/**
 * Gets the value of the given URL parameter.
 * 
 * @param sParam
 *            the parameter to get
 * @returns the value of the given url param
 */
utils.getURLParameter = function(param) {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for ( var i = 0; i < sURLVariables.length; i++) {
		var parameterName = sURLVariables[i].split('=');
		if (parameterName[0] == param) {
			return parameterName[1];
		}
	}
}

/**
 * Checks if the URL link has query parameters.
 */
utils.isURLParameterized = function(url) {
	return ((url.indexOf("?") != -1) && ((url.indexOf("=")) != -1));
}

/**
 * Extracts all query parameters from the given URL string
 * 
 * @param string
 *            the source URL string
 * @returns a string containing all query parameters from the url
 */
utils.extractQueryParams = function(string) {
	return string.substring(string.indexOf("?"));
}

/**
 * Extracts only the real URL part, ignoring the query parameters.
 * 
 * @returns the real URL part, without the query parameters
 */
utils.getPureURL = function() {
	var url = document.URL;
	if (url.indexOf("?") != -1) {
		var endIndex = url.indexOf("?");
		return url.substring(0, endIndex);
	}
	return url;
}

/**
 * Returns the current date as a string in format: dd/mm/yyyy
 */
utils.getCurrentDate = function() {
	var currentDate = new Date();
	var date = currentDate.getDate();
	if (date < 10) {
		date = "0" + date;
	}
	var month = currentDate.getMonth() + 1;
	if (month < 10) {
		month = "0" + month;
	}
	var year = currentDate.getFullYear();
	return date + "/" + month + "/" + year;
}

/**
 * Generates a URL (query) parameter with the given name, taking its value from
 * the input field with id - inputTextId.
 */
utils.generateParam = function(initialString, paramName, inputTextId) {
	if (document.getElementById(inputTextId).value.length == 0) {
		return "";
	}
	// if the field contains multiple comma-separated params, replace the commas
	// with &paramName=
	var fieldParams = this.trim(document.getElementById(inputTextId).value)
			.replace(/\s*,\s*/g, "&" + paramName + "=");
	if (initialString == "?") {
		return paramName + "=" + fieldParams;
	} else {
		return "&" + paramName + "=" + fieldParams;
	}
}

/**
*	Gets the base URL of the site, cutting out the current page (everything after the last slash). 
*	Example:
* 	http://localhost/VersatileShop/search.php 
*	will result in: http://localhost/VersatileShop
**/
utils.getBaseURL = function() {
	return document.URL.substring(0, document.URL.lastIndexOf("/"));
}
