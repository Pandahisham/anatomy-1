<?php if (!$this->session->userdata('user_id')) { ?>
<div class="home-bubble round-big shadow-big">
	<div class="title">Log in</div>
	The Anatomy Quiz Bank has over 2700 questions divded among 4 modules and 39 lectures. It aids in testing your knowledge for the Anatomy course at the University of Texas Health Science Center at San Antonio, and <strong>anonymously</strong> tracks and compares your quiz grades with fellow classmates.
	<div class="form">
	<form method="post" action="<?= site_url('account/signin') ?>">
		<table>
			<tr>
				<td width="100px">Email</td>
				<td><input type="text" class="genInput" name="email" maxlength="80" size="30" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" class="genInput" name="password" maxlength="12" size="30" /></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><input type="submit" class="genBtn" value="Log in" /></td>
			</tr>
		</table>
	</form>
	</div>
</div>

<div class="home-bubble round-big shadow-big">
	<div class="title">Sign up</div>
	Registering is completely free, but requires a valid UTHSCSA email address. <strong>Do not</strong> use the same password as your email account. Instead, create a unique password that is 6-12 characters long.
	<div class="form">
	<form method="post" action="<?= site_url('account/register') ?>">
		<table>
			<tr>
				<td width="100px"><span class="f-btn" alt="~@livemail.uthscsa.edu">Email</span></td>
				<td><input type="text" class="genInput" name="email" maxlength="80" size="30" /></td>
			</tr>
			<tr>
				<td><span class="f-btn" alt="6-12 characters">Password</span></td>
				<td><input type="password" class="genInput" name="password" maxlength="12" size="30" /></td>
			</tr>
			<tr>
				<td>Verify Password</td>
				<td><input type="password" class="genInput" name="vpassword" maxlength="12" size="30" /></td>
			</tr>
			<tr>
				<td>Class Year</td>
				<td><select class="genInput" name="class">
					<option value="" selected="selected"></option>
					<?php foreach ($classes as $c) { ?>
						<option value="<?= $c['id'] ?>"><?= $c['year'] ?></option>
					<?php } ?>
				</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><input type="submit" class="genBtn" value="Sign up" /></td>
			</tr>
		</table>
	</form>
	</div>
</div>
<?php } else { ?>
<div class="home-option round-big shadow-big">
	<div class="title">Lecture Questions</div>
	<div class="info">Answer questions for any lecture you choose. This is for practice and does not affect your Scorecard.</div>
	<a href="<?= site_url('anatomy/lecture') ?>" class="button round">Continue</a>
</div>

<div class="home-option round-big shadow-big">
	<div class="title">Module Questions</div>
	<div class="info">Answer questions for any module you choose. This is for practice and does not affect your Scorecard.</div>
	<a href="<?= site_url('anatomy/module') ?>" class="button round">Continue</a>
</div>

<div class="home-option round-big shadow-big">
	<div class="title">Take a Quiz</div>
	<div class="info">Answer questions for any lecture you choose. This is recorded onto your Scorecard and ranks with other students.</div>
	<a href="<?= site_url('anatomy/quiz') ?>" class="button round">Continue</a>
</div>

<div class="home-option round-big shadow-big">
	<div class="title">Course Questions</div>
	<div class="info">Answer questions for the entire course. This is for practice and does not affect your Scorecard.</div>
	<a href="<?= site_url('anatomy/course') ?>" class="button round">Continue</a>
</div>

<?php } ?>