<div id="content">
	<p class="title"><?= $title ?></p>

	<div id="reg_info" class="link-c round">
		Enter your email address and password to be a part of the best thing since macaroni and cheese.
		<p>Not a member? <a href="<?= site_url('account/register') ?>">Register</a></p>
	</div>

	<div id="reg_form">
		<p class="red"><?= $this->session->flashdata('error') ?></p>
		<form method="post" action="<?= site_url('account/signin') ?>">
			<table cellpadding="2" cellspacing="2">
				<tr>
					<td width="100px">Email</td>
					<td><input type="text" name="email" class="genInput" maxlength="80" size="30" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="password" class="genInput" maxlength="12" size="30" /></td>
				</tr>
				<tr>
					<td></td>
					<td align="right"><input type="Submit" value="Sign in" class="genBtn" /></td>
				</tr>
			</table>
		</form>
	</div>

	<div class="clear"></div>
	
	<div id="reg_links" class="link-c">
		<a href="<?= site_url('account/resend') ?>">Resend my activation code</a> <br />
		<a href="<?= site_url('account/recover') ?>">Recover my account</a>
	</div>
</div>