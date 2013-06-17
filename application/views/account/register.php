<div id="content">
	<p class="title"><?= $title ?></p>
		
	<div id="reg_info" class="round link-c">
		Creating an account is quick and painless. All you need is a valid University of Texas Health Science Center at San Antonio email address. A confirmation email will be sent to the provided address to activate your account.
	</div>

	<div id="reg_form" class="link-c">
		<?php if (!empty($error)) {
			echo "<p class=\"red\">";
			foreach ($error as $e) echo $e . "<br />";
			echo "</p>";
		} if (!$success) { ?>
		<form method="post" action="<?= site_url('account/register') ?>">
			<table cellpadding="2" cellspacing="2">
				<tr>
					<td width="100px"><span class="f-btn" alt="~@livemail.uthscsa.edu">Email</span></td>
					<td><input type="text" name="email" class="genInput" maxlength="80" size="30" value="<?php echo set_value('email') ?>" /></td>
				</tr>
				<tr>
					<td><span class="f-btn" alt="6-12 characters">Password</span></td>
					<td><input type="password" name="password" class="genInput" maxlength="12" size="30" value="<?php echo set_value('password') ?>" /></td>
				</tr>
				<tr>
					<td>Verify Password</td>
					<td><input type="password" name="vpassword" class="genInput" maxlength="12" size="30" value="<?php echo set_value('vpassword') ?>" /></td>
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
					<td align="right"><input type="Submit" value="Sign up" class="genBtn" /></td>
				</tr>
			</table>
		</form>
			<?php } else {
				echo "<p><strong>Thank you for registering</strong></p>An activation key has been sent to the provided email address. In case you do not receive it and it is not inside your email's junk folder, you may have it <a href=\"" . site_url('account/resend') . "\">resent</a>.";
			} ?>
	</div>

	<div class="clear"></div>
	
	<div id="reg_links" class="link-c">
		<a href="<?= site_url('account/resend') ?>">Resend my activation code</a> <br />
		<a href="<?= site_url('account/recover') ?>">Recover my account</a>
	</div>
</div>