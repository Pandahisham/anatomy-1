<div id="content">
	<p class="title"><?= $title ?></p>

	<p align="center" class="link-c"><strong>
		<?php if ($request == 'active')
			echo "Account successfully activated! You may now <a href=\"" . site_url('account/signin') . "\">Sign in</a>.";
		else if ($request == 'email')
			echo "Your email has been changed successfully.";
		else if (!$request)
			echo "The request could not be made because no request exists. If you think this is an error, please contact Lucy with details.";
		?>
	</strong></p>
</div>