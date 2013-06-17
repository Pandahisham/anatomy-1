<div id="content">
	<div class="title"><?= $title ?></div>
	<?php
	if (!empty($scores)) { ?>
	<table id="scores" cellpadding="0" cellspacing="0" class="link-c">
		<tr class="head">
			<td width="200px">Lecture</td>
			<td width="100px">Lowest Score</td>
			<td width="100px">Highest Score</td>
			<td width="100px">Best Time</td>
			<td width="100px">Class Average</td>
			<td width="100px">Your Average</td>
		</tr>
		<?php
		$i = 0;
		foreach ($scores as $id => $s)
		{
		if ($i % 2 == 0) { ?>
		<tr class="even">
		<?php } else { ?>
		<tr>
		<?php } ?>
			<td><a href="<?= site_url('anatomy/quiz') .'/'.$id ?>"><?= $s['name'] ?></a></td>
			<td><?= $s['me']['lowest'] ?>%</td>
			<td><?= $s['me']['highest'] ?>%</td>
			<td><?= date("i:s", $s['me']['time']) ?></td>
			<td><?php if (!empty($s['class'])) echo $s['class']; else echo "0"; ?>%</td>
			<td><?= $s['me']['average'] ?>%</td>
		</tr>
		<?php $i++; } ?>
	</table>
	<?php } else { ?>
	<strong>You have not taken any quizzes yet.</strong>
	<?php } ?>
</div>