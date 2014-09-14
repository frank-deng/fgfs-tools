<?php
require('FGTools.php');
require('config.php');

$fg = NULL;
$page_all = array(
	'777-300' => '777-300.php',
	'777-300ER' => '777-300.php',
	'bluebird' => 'bluebird.php',
);
try {
	$fg = new FGTelnet(FG_HOST, FG_PORT);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'/>
		<title>FlightGear</title>
	</head>
	<body>
		<p>
| <a href='sound.php?enabled=true'>Enable Sound</a>
| <a href='sound.php?enabled=false'>Mute Sound</a>
|</p>
		<p>Model: <?=$fg->get('/sim/description')?></p>
		<p>
<?php
	$time_utc = new DateTime(sprintf('%s-%s-%s %s:%s:%s', 
		(int)($fg->get('/sim/time/utc/year')),
		(int)($fg->get('/sim/time/utc/month')),
		(int)($fg->get('/sim/time/utc/day')),
		(int)($fg->get('/sim/time/utc/hour')),
		(int)($fg->get('/sim/time/utc/minute')),
		(int)($fg->get('/sim/time/utc/second'))
	));
	$time_local = clone $time_utc;
	$local_offset = (int)($fg->get('/sim/time/local-offset'));
	$time_local_offset = new DateInterval('PT'.abs($local_offset).'S');
	if ($local_offset < 0) {
		$time_local_offset->invert = 1;
	}
	$time_local->add($time_local_offset);
	echo sprintf('Time: %s UTC', $time_utc->format('Y-m-d H:i:s'))."<br/>\n";
	echo sprintf('Local time: %s', $time_local->format('Y-m-d H:i:s'))."\n";
?>
		</p>
		<p>
<?php
	$longitude = $fg->get('/position/longitude-deg');
	$latitude = $fg->get('/position/latitude-deg');
	$altitude = $fg->get('/position/altitude-ft');
	$velocity = (int)($fg->get('/velocities/groundspeed-kt'));
	if ('ufo' == $fg->get('/sim/flight-model')) {
		$velocity = (int)($fg->get('/velocities/equivalent-kt'));
	}
	echo 'Longitude: '.$longitude.($longitude >= 0 ? 'E' : 'W')."<br/>\n";
	echo 'Latitude: '.$latitude.($latitude >= 0 ? 'N' : 'S')."<br/>\n";
	echo sprintf('Altitude: %.1fft', $altitude)."<br/>\n";
	echo sprintf('Velocity: %dknots', $velocity)."\n";
?>
		</p>
		<p>
<?php
	$dist_remaining = (float)($fg->get('/autopilot/route-manager/distance-remaining-nm'));
	$dist_total = (float)($fg->get('/autopilot/route-manager/total-distance'));
	$dist_elapsed = $dist_total - $dist_remaining;
	echo sprintf('Total Distance: %.1fnmi', $dist_total)."<br/>\n";
	echo sprintf('Total Remaining Distance: %.1fnmi', $dist_remaining)."<br/>\n";
	echo sprintf('Total Elapsed Distance: %.1fnmi', $dist_elapsed)."<br/>\n";

	$time_elapsed_sec = (int)($fg->get('/autopilot/route-manager/flight-time'));
	$time_elapsed = new DateInterval2('PT'.$time_elapsed_sec.'S');
	$time_elapsed->recalculate();
	echo 'Flight Time: '
		.($time_elapsed->d > 0 ? (string)($time_elapsed->d).' days ': '')
		.$time_elapsed->format('%H:%I:%S')."<br/>\n";

	$time_remaining_sec = (int)($fg->get('/autopilot/route-manager/ete'));
	$time_remaining = new DateInterval2('PT'.$time_remaining_sec.'S');
	$time_remaining->recalculate();
	echo 'Total Time Remining: '
		.($time_remaining->d > 0 ? (string)($time_remaining->d).' days ': '')
		.$time_remaining->format('%H:%I:%S')."<br/>\n";
?>
		</p>
<?php
	if (strcmp('ufo', $fg->get('/sim/flight-model'))) {
	?><p><?php
		echo sprintf('Fuel Remaining: %.2f pounds / %.2f gallons / %.1f%%',
			(float)($fg->get('/consumables/fuel/total-fuel-lbs')),
			(float)($fg->get('/consumables/fuel/total-fuel-gal_us')),
			(float)($fg->get('/consumables/fuel/total-fuel-norm')) * 100
		)."\n";
	?></p><?php
	}
?>
<?php
	if (bool($fg->get('/instrumentation/gps/wp/wp[1]/valid'))) {
	?><p><?php
		echo sprintf('Next Destination: %s',
			$fg->get('/instrumentation/gps/wp/wp[1]/ID'))."<br/>\n";
		echo sprintf('Distance remaining: %.1fnmi',
			(float)($fg->get('/instrumentation/gps/wp/wp[1]/distance-nm')))
			."<br/>\n";
		echo sprintf('Time remaining: %s',
			$fg->get('/instrumentation/gps/wp/wp[1]/TTW'))."<br/>\n";
		echo sprintf('Bearing: %d',
			(int)($fg->get('/instrumentation/gps/wp/wp[1]/bearing-mag-deg')))."\n";
	?></p><?php
	}
?>
<?php
	if (ispaused($fg)) {
		echo '<p>Simulation paused.</p>';
	}
?>
<?php
	/*
	$img_path = screenshot($fg);
	if (NULL != $img_path) {
		?><img src='<?=$img_path?>'/><?php
	}
	 */
?>
<?php
	$page = $page_all[$fg->get('/sim/aircraft')];
	$page = (NULL == $page ? 'generic.php' : $page);
	include_once($page);
?>
	</body>
</html>

<?php
} catch (Exception $e) {
	header(sprintf('Location: fail.php?message=%s', urlencode($e->getMessage())));
	exit(1);
}
?>

