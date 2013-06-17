<p class="title"><?= $title ?></p>
<br />
<div>
	You are out of time. All remaining questions will be marked as wrong. Better study more!<br />
	<form method="post" action="<?= site_url('anatomy/quitQuizConfirm') ?>">
		<p align="center"><input type="submit" id="yesno" value="Okay" class="genBtn" /></p>
	</form>
</div>
<div class="clear"></div>