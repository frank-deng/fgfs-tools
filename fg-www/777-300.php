<?php
if (bool($fg->get('/autopilot/settings/descending-manager-running'))){
	$descending_distance = (float)($fg->get('/autopilot/settings/descending-distance'));
	$altitude_pause = (float)($fg->get('/autopilot/settings/altitude-pause'));
	$vertical_speed = (int)($fg->get('/autopilot/settings/vertical-speed-fpm'));

	echo '<p>* Descending Manager activated.<br/>';
	if ($vertical_speed < 0) {
		echo '* Simulation will be paused when the altitude is lower than '
			.sprintf('%.1f', $altitude_pause).'ft.';
	} else {
		echo '* Start descending when the remaining distance is shorter than '
			.sprintf('%.1f', $descending_distance).'nmi.';
	}
	echo "</p>\n";
} else {
?>
	<p><b>WARNING</b>: Descending Manager not activated.</p>
<?php
}
?>

