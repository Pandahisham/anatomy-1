/** Popup box **/
	var scro = [0,0];
	var dimen = [0,0];
function popup(page, imp) {
	// imp is of the form "varname=foo&varnam2=bar"...
	closePopup(); // in case popup is already active
	$(document.body).append("<div id=\"popup-bg\"></div>");
	$("#popup-bg").show();
	$("#pucon").html("<img src=\"" + media_url + "images/loader.gif\" />");
	$("#popup").fadeIn();
	$.ajax({
		type: "GET",
		url: base_url + page,
		data: imp,
		success: function(data) {
			$("#pucon").html(data);
			posPopup();
		}
	});
}
/** Close popup **/
function closePopup() {
	$("#popup").fadeOut();
	$("#popup-bg").remove();
}
/** Reposition popup **/
function posPopup() {
	dimen = windowSize();
	scro = getScroll();
	var pHeight = $("#popup").height();
	var pWidth = $("#popup").width();
	$("#popup").css({ 'top': Math.floor((dimen[1] - pHeight) / 4) , 'left': Math.floor((dimen[0] - pWidth) / 2) + scro[0] });
}

/** Get browser's scroll height and width **/
function getScroll() {
	if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scro[1] = window.pageYOffset;
		scro[0] = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		//DOM compliant
		scro[1] = document.body.scrollTop;
		scro[0] = document.body.scrollLeft;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scro[1] = document.documentElement.scrollTop;
		scro[0] = document.documentElement.scrollLeft;
	}
	return scro;
}

/** Get browser's inner height and width **/
function windowSize() {
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		dimen[0] = window.innerWidth;
		dimen[1] = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		dimen[0] = document.documentElement.clientWidth;
		dimen[1] = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		dimen[0] = document.body.clientWidth;
		dimen[1] = document.body.clientHeight;
	}
	return dimen;
}

$(function() {
	posPopup('popup');
	
	/** "title" hover **/
	$(".f-btn").mouseenter(function() {
		var offset = $(this).offset();
		$("#title").html($(this).attr("alt")).show();
		$("#title").css({left: offset.left, top: offset.top - 23});
	}).mouseleave(function() {
		$("#title").hide();
	});
});
