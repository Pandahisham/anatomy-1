<?php
if ( $css ) {
	if ( is_array($css) ) {
		foreach ($css as $c)
			echo $c;
	} else
		echo $css;
} 
if ( $js ) {
	if ( is_array($js) ) {
		foreach ($js as $j)
			echo $j;
	} else
		echo $js;
} ?>

<?= $contents ?>