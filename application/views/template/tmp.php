<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>	
		<title>UTHSCSA Anatomy Quiz Bank - <?= $title ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="description" content="UTHSCSA Anatomy question and answer quiz bank." />
		<meta name="keywords" content="galen, university of texas health science center, uthscsa, quiz, bank, quizzes, practice, answers, questions, tests" />
		<link rel="shortcut icon" href="<?= base_url() ?>images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/site.css" />
		<?php
		if ( $css ) {
			if ( is_array($css) ) {
				foreach ($css as $c)
					echo $c;
			} else
				echo $css;
		} ?>
		<script type="text/javascript">
			var base_url = '<?= base_url() ?>index.php/';
			var media_url = '<?= base_url() ?>';
		</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>js/site.js"></script>
		<?php
		if ( $js ) {
			if ( is_array($js) ) {
				foreach ($js as $j)
					echo $j;
			} else
				echo $js;
		} ?>
	</head>
	
	<body>
		
	<!-- BEGIN Header -->
	<div id="top-nav">
		<div class="container">
	<?php if (!$this->session->userdata('user_id')) { ?>
		<span class="link-c">Welcome, <a href="<?= site_url('account/signin') ?>">Sign in</a> or <a href="<?= site_url('account/register') ?>">Register</a></span>
	<?php } else { ?>
		<ul id="ulinks" class="link-c">
			<li><a href="<?= site_url('home') ?>">Home</a>
			<li><a href="<?= site_url('home/scorecard') ?>">Scorecard</a>
			<li><a href="<?= site_url('account') ?>">Settings</a></li>
			<li><a href="<?= site_url('account/goodbye') ?>">Sign Out</a></li>
		</ul>
	<?php } ?>
		</div>
	</div>
	
	<div class="container">
		
		<div id="galen-title"><a href="<?= site_url('home') ?>">UTHSCSA Anatomy Quiz Bank</a></div>
		<div class="clear"></div>
		<!-- END Header -->
		
		<!-- BEGIN Content -->
		<?= $contents ?>
		<!-- END Content -->
		
		<div class="clear"></div>
		
		<!-- BEGIN Footer -->
		<div id="footer">
			&copy; 2011 Justin Pope. All questions and answers belong to their respective owners.<br/>
			Contact <a href="mailto:chenlx@livemail.uthscsa.edu?subject='Anatomy Website'">Lucy Chen</a> to report errors or praise her.
		</div>
		<!-- END Footer -->
			
	</div>

	<!-- Title div hidden by default -->
	<div id="title" class="shadow-off"></div>
	<!-- Popup box hidden by default -->
	<div id="popup">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td class="tlcorner"></td>
			<td class="hborder"></td>
			<td class="trcorner"></td>
		</tr>
		<tr>
			<td class="vborder"></td>
			<td><div id="pucon"></div><div id="puclose"><a href="#" onclick="closePopup(); return false;"><img src="<?= base_url() . 'images/close.gif' ?>" border="0" /></a></div></td>
			<td class="vborder"></td>
		</tr>
		<tr>
			<td class="blcorner"></td>
			<td class="hborder"></td>
			<td class="brcorner"></td>
		</tr>
		</table>
	</div>
	</body>
	
</html>