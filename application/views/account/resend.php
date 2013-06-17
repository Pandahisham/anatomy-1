<div id="content">
	<p class="title"><?= $title ?></p>

	<div id="reg_info" class="round">
		Enter your account's email address and an email with the activation code will be sent shortly.
		<p>Also be certain to check your email's junk folder.</p>
	</div>

	<div id="reg_form">
		<?php if ($this->session->flashdata('error'))
			echo "<p class=\"red\">" . $this->session->flashdata('error') . "</p>";
		if (!$success) { ?>
		<form method="post" action="<?= site_url('account/resend') ?>">
			<input type="text" name="email" class="genInput" maxlength="80" size="30" /> <input type="Submit" value="Send" class="genBtn" />
		</form>
		<?php } else { ?>
			<p><strong>An email with the activation key has been sent to the provided address.</strong></p>
		<?php } ?>
	</div>
</div>