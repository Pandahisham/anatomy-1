/**
	Verifies if the answer to the question is correct
	@return null
**/
function answer()
{
	var answer = $('input[name="answer"]:checked').val();
	if (answer != undefined)
	{
		// Hide the button
		$("#btnAnswer").hide();
		// Show the loader
		$("#loader").show();
		
		$.ajax({
			type: "GET",
			url: base_url + "anatomy/submitAnswer",
			data: { answer: answer },
			success: function(data) {				
				$("#quiz_a").find("#answer" + data).parent("li").addClass("correct-bg");
				// Wrong Answer
				if (data != answer)
				{
					$("#quiz_a").find("#answer" + answer).parent("li").addClass("wrong-bg");
					
					// Update wrong stat
					$("#wrong").html(parseInt($("#wrong").text()) + 1);
				}
				// Right answer
				else
					$("#correct").html(parseInt($("#correct").text()) + 1);

				// Update the total answered
				$("#answered").html(parseInt($("#answered").text()) + 1);
				// Update the score
				$(".score").html(Math.floor((parseInt($("#correct").text()) / parseInt($("#total").text())) * 100) + "%");
				// Hide the loader
				$("#loader").hide();
				// Show the next button
				if (parseInt($("#answered").text()) < parseInt($("#total").text()))
					$("#btnNext").show();
				else
					$("#btnNext").remove();
			}
		});
	}
}

/**
	Finds the next question for this user
	@param	session	user's session id
	@return	null
**/
function nextQuestion(session)
{
	$("#btnNext").hide();
	$("#loader").show();
	$.ajax({
		type: "GET",
		url: base_url + "anatomy/nextQuestion",
		data: { session: session },
		success: function(data) {
			$("li.wrong-bg").removeClass("wrong-bg");
			$("li.correct-bg").removeClass("correct-bg");
			data = jQuery.parseJSON(data);

			// Update all the fields
			$("#question").text(data.question.question);
			$("#question_no").html(parseInt($("#question_no").text()) + 1);
			$("#answerA").html(data.question.a1);
			$("#answerB").html(data.question.a2);
			$("#answerC").html(data.question.a3);
			$("#answerD").html(data.question.a4);
			$("#answerE").html(data.question.a5);
			setRank(data.question.rank);
			
			// Hide/show buttons
			$("#loader").hide();
			$("#btnAnswer").show();

		}
	});
	document.qanda.reset();
}

/**
	Dynamically sets the rank stars
	@param	rank	this question's rank
	@return	null
**/
function setRank(rank)
{
	var count = 1;
	$("#ranks").find(".star").each(function() {
		$(this).show();
		if (count <= rank)
			$(this).attr("src", media_url + "images/fstar.jpg");
		else
			$(this).attr("src", media_url + "images/estar.jpg");
		count++;
	});
}

/**
	Sets the new rank for this question
	@param	rank	the rank
	@return	null
**/
function changeRank(rank)
{
	$("#ranks").find(".star").hide();
	$("#star_loader").show();
	$.ajax({
		type: "GET",
		url: base_url + "anatomy/changeRank",
		data: { rank: rank },
		success: function(data) {
			$("#star_loader").hide();
			setRank(rank);
		}
	});
}

/**
	Deletes the session and marks the scorecard
	@return	null
**/
function quitQuizConfirm()
{
	$.ajax({
		type: "GET",
		url: base_url + "anatomy/quitQuiz",
		data: {  },
		success: function(data) {
			// Do nothing
		}
	});
}

/**
	Controls the timer for quizzes
	@return	null
**/
var current = 0;
var total = 0;
var min = 0;
var sec = 0;
var start = 0;
function timer(fresh)
{
	if (fresh)
	{
		total = parseInt($("#total").text());
		start = parseInt($("#start").text());
		$(".time").html(total + ":00");
		min = total;
		current = start;
		setTimeout('timer(false)', 1000);
	}
	else if (current >= (total * 60 + start))
	{
		$("#quiz_q").remove();
		popup('anatomy/timeout', '');
	}
	else
	{
		if (sec == 0)
		{
			min--;
			sec = 59;
		}
		else
			sec--;
		if (sec >= 0 && sec < 10)
			$(".time").html(min + ":0" + sec);
		else
			$(".time").html(min + ":" + sec);
		
		setTimeout('timer(false)', 1000);
	}
	current++;
}

$(function() {
	$("#keywords").focus(function() {
		if ($("#keywords").val() == "Keywords")
			$("#keywords").val("");
	}).blur(function() {
		if ($("#keywords").val() == "")
			$("#keywords").val("Keywords");
	});
	
	$("#ranks").find(".star").click(function(e) {
		e.preventDefault();
		changeRank($(this).index() + 1);
	});

});