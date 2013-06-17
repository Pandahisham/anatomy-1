<?php
	$json = json_decode($data['session']['filters'], true);
	$exam = $json['exam'];
	$rank = $json['rank'];
	$keywords = $json['keywords'];
?>
<div id="content">
	<div class="title"><div id="sm-title"><?= $title ?></div><div id="ranks">
	<?php if ($data['rank'] >= 0 && !empty($data['question'])) { foreach ($ranks as $r) {
	if ($r['id'] <= $data['rank']) { ?>
		<img src="<?= base_url() . 'images/fstar.jpg'?>" alt="<?= $r['name'] ?>" class="f-btn star" />
	<?php } else { ?>
		<img src="<?= base_url() . 'images/estar.jpg'?>" alt="<?= $r['name'] ?>" class="f-btn star" />
	<?php } } } ?>
	<img src="<?= base_url() . 'images/loader.gif' ?>" id="star_loader" />
	</div></div>

	<div id="quiz_q">
		<?php if (!empty($data['question'])) { ?>
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
		<?php } else { ?>
		<strong>There were no questions that matched your filters.</strong>
		<?php } ?>
	</div>
	
	<div id="quiz_info" class="round-big">
		<div class="score"><?php if ($data['session']['total'] > 0) echo floor(($data['session']['correct'] / $data['session']['total']) * 100); else echo "0"; ?>%</div>
		<div class="time">No time limit</div>
		<div><span id="answered"><?= $data['session']['correct'] + $data['session']['wrong'] ?></span> out of <span id="total"><?= $data['session']['total'] ?></span> answered</div>
		<div><span id="correct"><?= $data['session']['correct'] ?></span> <span class="green">correct</span></div>
		<div><span id="wrong"><?= $data['session']['wrong'] ?></span> <span class="red">wrong</span></div>
		<div class="quiz_links link-c">
		<a href="<?= site_url('anatomy/course') . '/new' ?>">Start Over</a><br />
		</div>
	</div>
	
	<div class="filters">
		<form method="post" action="<?= site_url('anatomy/setFilters/course')?>">
			<input type="text" id="keywords" value="<?php if ($keywords != "") echo $keywords; else echo "Keywords"; ?>" class="genInput" name="keywords" maxlength="32" />
			<select class="genInput" name="rank">
				<option value="">Difficulty</option>
				<?php if (!empty($filters['ranks'])) {
				foreach ($filters['ranks'] as $f) { ?>
				<option value="<?= $f['id'] ?>"<?php if ($rank == $f['id']) echo " selected=\"selected\""?>><?= $f['name'] ?></option>
				<?php } } ?>
			</select>
			<select class="genInput" name="exam">
				<option value="">Exam Date</option>
				<?php if (!empty($filters['exams'])) {
				foreach ($filters['exams'] as $e) { ?>
				<option value="<?= $e['test_no'] ?>"<?php if ($exam == $e['test_no']) echo " selected=\"selected\""?>><?= substr($e['test_no'], 5, 2).'/'.substr($e['test_no'], 8).'/'.substr($e['test_no'], 0, 4)?></option>
				<?php } } ?>
			</select> 
			<input type="submit" value="Filter" class="genBtn" />
		</form>
	</div>
</div>