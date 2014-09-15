<?php
if (!strcmp('1', $fg->get('/autopilot/settings/pause-manager-enabled'))) {
	$distance = (float)($fg->get('/autopilot/settings/pause-manager-distance'));
	echo '<p>* Pause Manager activated.<br/>';
	echo '* Simulation will be paused when the remaining distance is shorter than '
		.sprintf('%.1f', $distance).'nmi.</p>'."\n";
}
?>

