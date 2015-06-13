<?php
define('FG_HOST', 'localhost');
define('FG_PORT_BASE', 5400);

require_once('sysinfo.php');
require_once('FGTelnet.php');
require_once('fgreport.php');

//Fetch System Information
$cpu_temp = getcputemperature();
$gpu_temp = getgputemperature();
$cpu_temp = ($cpu_temp ? (string)$cpu_temp.'&deg;C' : 'N/A');
$gpu_temp = ($gpu_temp ? (string)$gpu_temp.'&deg;C' : 'N/A');

?><!DOCTYPE html>
<html>
	<head>
		<meta name='viewport' id='viewport' content='width=400px, target-densitydpi=device-dpi, user-scalable=no'/>
		<meta charset='UTF-8'/>
		<title>FlightGear Report</title>
		<link href='style.css' rel='stylesheet' type='text/css'/>
		<style type='text/css'>
			body {
				max-width: 620px;
				margin-left: auto;
				margin-right: auto;
				padding: 3px 10px;
				font-size: 14px;
			}
			table { 
				border-spacing: 0;
				border-collapse: collapse;
				margin-top: 6px;
			}
			h2 {
				font-size: 16px;
				margin-top: 10px;
				margin-bottom: 0px;
				padding-bottom: 2px;
				border-bottom: 1px solid black;
			}
			table th {
				text-align: left;
				padding-top: 4px;
			}
			table td {
				padding-right: 10px;
			}
		</style>
	</head>
	<body>
		<h2>System Information</h2>
		<table>
			<tr><td>CPU Temperature:&nbsp;</td><td><?=$cpu_temp?></td></tr>
			<tr><td>GPU Temperature:&nbsp;</td><td><?=$gpu_temp?></td></tr>
		</table>
<?php
	for ($i = 0; $i <= 9; $i++) {
		$report = getfgreport(FG_HOST, FG_PORT_BASE, $i);
		if ($report) {
			echo "<h2>Instance $i</h2>\n";
			?>
			<table>
				<tr><th colspan='2'>Basic information</th></tr>
				<tr><td>Aircraft</td><td><?=$report['aircraft']?></td></tr>

				<tr><th colspan='2'>Time</th></tr>
				<tr><td>Real-world Time</td><td><?=$report['real_world_time']?></td></tr>
				<tr><td>UTC Time</td><td><?=$report['utc_time']?></td></tr>
				<tr><td>Local Time</td><td><?=$report['local_time']?></td></tr>

				<tr><th colspan='2'>Position</th></tr>
				<tr><td>Latitude/Longitude</td><td><?=sprintf('%.6f %.6f', $report['latitude'], $report['longitude'])?></td></tr>
				<tr><td>Altitude</td><td><?=$report['altitude']?> ft</td></tr>
				<tr><td>AGL</td><td><?=$report['agl']?> ft</td></tr>
				<tr><td>Heading</td><td><?=sprintf('%.1f', $report['heading'])?></td></tr>

				<tr><th colspan='2'>Velocity</th></tr>
			<?php
				if ($report['fdm'] != 'ufo') {
					?>
					<tr><td>Indicated Air Speed</td><td><?=$report['indicated_air_speed']?> kts</td></tr>
					<tr><td>Real Air Speed</td><td><?=$report['air_speed']?> kts</td></tr>
					<tr><td>Ground Speed</td><td><?=$report['ground_speed']?> kts</td></tr>
					<tr><td>Mach</td><td><?=sprintf('%.3f', $report['mach'])?></td></tr>
					<tr><td>Vertical Speed</td><td><?=(int)($report['vertical_speed'] * 60)?> fpm</td></tr>
					<?php
				} else {
					echo '<tr><td>Speed</td><td>'.$report['equivalent_speed'].' kts</td></tr>';
				}
			?>

			<?php
				if ($report['autopilot_active']) {
					?>
					<tr><th colspan='2'>Autopilot</th></tr>
					<tr><td>Total Distance</td><td><?=sprintf('%.2f', $report['total_distance'])?> nmi</td></tr>
					<tr><td>Distance Remaining</td><td><?=sprintf('%.2f', $report['distance_remaining'])?> nmi</td></tr>
					<tr><td>Distance Elapsed</td><td><?=sprintf('%.2f', $report['distance_elapsed'])?> nmi</td></tr>
					<tr><td>Flight Time</td><td><?=$report['flight_time']?></td></tr>
					<tr><td>Remaining Time</td><td><?=$report['time_remaining']?></td></tr>
					<?php
				}
			?>
				<tr><th colspan='2'>Misc</th></tr>
			<?php
				if ($report['fdm'] != 'ufo') {
					echo '<tr><td>Fuel Remaining</td><td>'.sprintf('%.2f%%', $report['fuel'] * 100).'</td></tr>'."\n";
				}
			?>
				<tr><td>Crashed</td><td><?=$report['crashed'] ? 'Yes' : 'No'?></span></td></tr>
				<tr><td>Paused</td><td><?=$report['paused'] ? 'Yes' : 'No'?></span></td></tr>
			</table>
			<?php
		}
	}
?>
	</body>
</html>

