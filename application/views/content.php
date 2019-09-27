<?php
$style = '';
if (isset($bg))
	{
		if (substr($bg,0,1) == '#')
			{
				$style = ' background-color: '.$bg.';';
			}
	}
if (isset($content)) {
	echo '<!--- content--->' . cr();
	if (isset($fluid)) {
		echo '<div class="container-fluid" style="'.$style.'">', cr();
	} else {
		echo '<div class="container" style="'.$style.'">', cr();
	}

	echo $content;
	echo '</div>' . cr();
}
?>