<p class="title"><?= $title ?></p>
<br />
<div>
	Are you sure you want to stop the quiz? If you do, all remaining questions will count as wrong answers.<br />
	<form method="post" action="<?= site_url('anatomy/quitQuizConfirm') ?>">
		<p align="center"><input type="submit" id="yesno" value="Yes" class="genBtn" /></p>
	</form>
</div>
<div class="clear"></div>