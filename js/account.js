$(function() {
	/** Account Settings tabbed divisons **/
	$("#rightContent > div:not(:first)").hide();
	$("#leftContent").find("li").click(function() {
		$("#leftContent").find(".selected").removeClass("selected");
		$(this).addClass("selected");
		$("#rightContent > div").hide();
		$("#rightContent > div:eq(" + $(this).index() + ")").show();
	});
});