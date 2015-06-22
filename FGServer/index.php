<?php
require_once('lib/fgreport.php');

//Fetch System Information
$cpu_temp = cpu_temperature();
$gpu_temp = gpu_temperature();
$cpu_temp = ($cpu_temp ? (string)$cpu_temp.'&deg;C' : 'N/A');
$gpu_temp = ($gpu_temp ? (string)$gpu_temp.'&deg;C' : 'N/A');

//Fetch reports for each instance
$report_all = Array();
for ($i = 0; $i < FG_INSTANCE_COUNT; $i++) {
	$r = fgfs_report($i);
	if ($r){
		$report_all[$i] = $r;
	}
}

?><!DOCTYPE html>
<html>
	<head>
		<meta name='viewport' id='viewport' content='width=746px, target-densitydpi=device-dpi, user-scalable=yes'/>
		<meta http-equiv='refresh' content='60'/>
		<meta charset='UTF-8'/>
		<title>FlightGear Report</title>
		<style type='text/css'>
			body {
				margin-left: auto;
				margin-right: auto;
				padding: 3px 10px;
				font-size: 14px;
				max-width: 740px;
			}
			table { 
				border-spacing: 0;
				border-collapse: collapse;
			}
			.warning {
				color: red;
				font-weight: bold;
			}
			h2 {
				font-size: 16px;
				margin-top: 10px;
				margin-bottom: 10px;
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

			.map {
				width: 720px;
				height: 450px;
				border: 1px solid black;
				background-image: url('image/map.jpg');
				background-size: 100% 100%;
				margin-left: auto;
				margin-right: auto;
				position: relative;
				overflow: hidden;
			}
			.map .pointer {
				width: 4px;
				height: 4px;
				line-height: 4px;
				margin: -2px;
				border: 1px solid black;
				border-radius: 10px;
				position: absolute;
				color: black;
				font-size: 4px;
				display: none;
				overflow: hidden;
				text-align: center;
			}
			h2 .pointer {
				display: inline-block;
				vertical-align: middle;
				width: 4px;
				height: 4px;
				line-height: 4px;
				border: 1px solid black;
				border-radius: 10px;
			}
			.pointer#p0{
				background-color: red;
			}
			.pointer#p1{
				background-color: lime;
			}
			.pointer#p3{
				background-color: cyan;
			}
			.pointer#p4{
				background-color: yellow;
			}
			<?php
			foreach ($report_all as $i => $report) {
				?>
				.map .pointer#p<?=$i?> {
					display: block;
					left: <?=(100 * ($report['longitude'] + 180) / 360)?>%;
					top: <?=(100 * (90 - $report['latitude']) / 180)?>%;
				}
				<?php
			}
			?>
		</style>
	</head>
	<body>
		<h2>System Information</h2>
		<table>
			<tr><td>CPU Temperature:&nbsp;</td><td><?=$cpu_temp?></td></tr>
			<tr><td>GPU Temperature:&nbsp;</td><td><?=$gpu_temp?></td></tr>
		</table>
		<?php
			if ($cpu_temp > 90 || $gpu_temp > 90) {
				echo '<p class="warning">WARNING: Overheat!!!</p>';
			}
		?>
		<h2>Map</h2>
		<div class='map'>
			<div class='pointer' id='p0'></div>
			<div class='pointer' id='p1'></div>
			<div class='pointer' id='p2'></div>
			<div class='pointer' id='p3'></div>
			<div class='pointer' id='p4'></div>
		</div>
		<p></p>
		<?php
		foreach ($report_all as $i => $report) {
			?>
			<?php
		}
		?>

<?php
	foreach ($report_all as $i => $report) {
		if ($report) {
			echo "<h2><div class='pointer' id='p$i'></div> Instance $i</h2>\n";
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
				<tr><td>Altitude</td><td><?=(int)$report['altitude']?> ft</td></tr>
				<tr><td>AGL</td><td><?=(int)$report['agl']?> ft</td></tr>
				<tr><td>Heading</td><td><?=sprintf('%.1f', $report['heading'])?></td></tr>

				<tr><th colspan='2'>Velocity</th></tr>
			<?php
				if ($report['fdm'] != 'ufo') {
					?>
					<tr><td>Indicated Air Speed</td><td><?=(int)$report['indicated_air_speed']?> kts</td></tr>
					<tr><td>Real Air Speed</td><td><?=(int)$report['air_speed']?> kts</td></tr>
					<tr><td>Ground Speed</td><td><?=(int)$report['ground_speed']?> kts</td></tr>
					<tr><td>Mach</td><td><?=sprintf('%.3f', $report['mach'])?></td></tr>
					<tr><td>Vertical Speed</td><td><?=(int)($report['vertical_speed'])?> fpm</td></tr>
					<?php
				} else {
					echo '<tr><td>Speed</td><td>'.(int)$report['equivalent_speed'].' kts</td></tr>';
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
				<tr><td>Frame Rate</td><td><?=$report['frame_rate']?></span></td></tr>
				<tr><td>Maximum Frame Rate</td><td><?=$report['fps_limit']?></span></td></tr>
			</table>
			<?php
		}
	}
?>
	</body>
</html>

