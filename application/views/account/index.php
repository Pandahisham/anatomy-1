<div id="content">
	<p class="title"><?= $title ?></p>

	<div id="leftContent">
		<ul class="link-c">
			<li class="selected"><a href="#">Change Email</a></li>
			<li><a href="#">Change Password</a></li>
		</ul>
	</div>
	
	<div id="rightContent">
			<?php if (!empty($error)) {
			echo "<span class=\"red\">";
			foreach ($error as $e)
			echo $e . "<br />";
			echo "</span><br />"; } ?>
		
		<div><p class="title">Change Email</p>
			<p>The new email does not have to be a university address. A verification code will be sent to the new email address, which must be used before any changes are made.</p>
			<form method="post" action="<?= site_url('account') ?>">
			<table cellpadding="2" cellspacing="2">
				<tr>
					<td width="100px">Email</td>
					<td><?= $user['email'] ?></td>
				</tr>
				<tr>
					<td>New Email</td>
					<td><input type="text" name="email" class="genInput" maxlength="80" size="30" value="<?php echo set_value('email') ?>" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Update" class="genBtn" /></td>
				</tr>
			</table>
			<input type="hidden" name="step" value="Email" />
			</form>
		</div>
		
		<div><p class="title">Change Password</p>
			<form method="post" action="<?= site_url('account') ?>">
			<table cellpadding="2" cellspacing="2">
				<tr>
					<td width="120px">Old Password</td>
					<td><input type="password" name="opassword" class="genInput" maxlength="12" size="30" /></td>
				</tr>
				<tr>
					<td><span class="f-btn" alt="6-12 characters">New Password</span></td>
					<td><input type="password" name="password" class="genInput" maxlength="12" size="30" /></td>
				</tr>
				<tr>
					<td>Verify Password</td>
					<td><input type="password" name="vpassword" class="genInput" maxlength="12" size="30" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Update" class="genBtn" /></td>
				</tr>
			</table>
			<input type="hidden" name="step" value="Password" />
			</form>
		</div>
	</div>
</div>