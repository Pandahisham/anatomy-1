<script type="text/javascript">$(function() { timer(true); });</script>
<div id="content">
	<div class="title"><?= $title ?></div>
	<?php if ($this->uri->segment(3) == "") { ?>
	
	<ul class="list link-c">
	<?php foreach ($lectures as $l) { ?>
		<li><a href="<?= site_url('anatomy/quiz') . '/' . $l['id'] ?>"><?= $l['name'] ?></a></li>
	<?php } ?>
	</ul>
	<?php
	} else { 
	?>
	<div id="quiz_q">
		<strong>Question #<span id="question_no"><?= $data['session']['correct'] + $data['session']['wrong'] + 1 ?></span></strong> <span id="question"><?= $data['question']['question'] ?></span>
		
		<form method="post" name="qanda" action="javascript:answer()">
		<ul id="quiz_a">
			<li><input type="radio" name="answer" value="A" /> <strong>A</strong> <span id="answerA"><?= $data['question']['a1'] ?></span></li>
			<li><input type="radio" name="answer" value="B" /> <strong>B</strong> <span id="answerB"><?= $data['question']['a2'] ?></span></li>
			<li><input type="radio" name="answer" value="C" /> <strong>C</strong> <span id="answerC"><?= $data['question']['a3'] ?></span></li>
			<li><input type="radio" name="answer" value="D" /> <strong>D</strong> <span id="answerD"><?= $data['question']['a4'] ?></span></li>
			<li><input type="radio" name="answer" value="E" /> <strong>E</strong> <span id="answerE"><?= $data['question']['a5'] ?></span></li>
		</ul>
		<img src="<?= base_url() . 'images/loader.gif' ?>" id="loader" />
		<input type="submit" value="Answer" class="genBtn" id="btnAnswer" /> 
		<input type="button" onClick="javascript:nextQuestion()" value="Next Question" class="genBtn" id="btnNext" />
		</form>
	</div>
	
	<div id="quiz_info" class="round-big">
		<div class="score"><?php if ($data['session']['total'] > 0) echo floor(($data['session']['correct'] / $data['session']['total']) * 100); else echo "0"; ?>%</div>
		<div class="time"></div><div id="start"><?= $data['session']['started'] ?></div>
		<div><span id="answered"><?= $data['session']['correct'] + $data['session']['wrong'] ?></span> out of <span id="total"><?= $data['session']['total'] ?></span> answered</div>
		<div><span id="correct"><?= $data['session']['correct'] ?></span> <span class="green">correct</span></div>
		<div><span id="wrong"><?= $data['session']['wrong'] ?></span> <span class="red">wrong</span></div>
		<div class="quiz_links link-c">
		<a href="#" onClick="javascript: popup('anatomy/quitQuiz', '')">Quit</a>
		</div>
	</div>
	<?php } ?>
</div>