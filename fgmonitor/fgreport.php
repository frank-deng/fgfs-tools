<?php
$FG_CONNECTIONS = Array(
	Array('host' => 'localhost', 'port' => 5410),
	Array('host' => 'localhost', 'port' => 5411),
	Array('host' => 'localhost', 'port' => 5412),
	Array('host' => 'localhost', 'port' => 5413),
	Array('host' => 'localhost', 'port' => 5414),
);

function fgfs_report($host, $port) {
	$conn = curl_init();
	curl_setopt($conn, CURLOPT_URL, $host.':'.$port.'/json/fgreport');
	curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($conn, CURLOPT_HEADER, 0);
	curl_setopt($conn, CURLOPT_TIMEOUT, 2);
	$fgreport_raw = curl_exec($conn);
	curl_close($conn);
	if (!$fgreport_raw) {
		return null;
	}

	$fgreport = json_decode($fgreport_raw, true);
	$result = Array();
	foreach ($fgreport['children'] as $i => $item) {
		switch ($item['type']) {
			case 'bool':
				$result[$item['name']] = ($item['value'] == 'true' ? true : false);
			break;
			case 'int':
				$result[$item['name']] = (int)($item['value']);
			break;
			case 'double':
				$result[$item['name']] = (double)($item['value']);
			break;
			default:
				$result[$item['name']] = $item['value'];
			break;
		}
	}
	return $result;
}

$report_all = Array();
foreach ($FG_CONNECTIONS as $i => $conn) {
	$report = fgfs_report($conn['host'], $conn['port']);
	if ($report) {
		$report['instance_num'] = $i;
		array_push($report_all, $report);
	}
}

?><!DOCTYPE html>
<html>
	<head>
		<meta name='viewport' id='viewport' content='width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no'/>
		<meta http-equiv="refresh" content="5"/>
		<meta charset='UTF-8'/>
		<title>FlightGear Report</title>
	</head>
	<body>
		<?php
if (count($report_all) == 0) {
	echo '<h3>No FlightGear instance is running.</h3>';
} else {
	foreach ($report_all as $i => $report) {
		if ($i > 0) {
			echo '<hr/>';
		}
		?>
			<h3><?=$report['aircraft']?></h3>
			<table>
				<tr><td colspan='2'><b>Time</b></td></tr>
				<tr><td>Real-world</td><td><?=$report['real_world_time']?></td></tr>
				<tr><td>UTC</td><td><?=$report['utc_time']?></td></tr>
				<tr><td>Local</td><td><?=$report['local_time']?></td></tr>

				<tr><td colspan='2'><br/><b>Aircraft</b></td></tr>
				<tr><td>Latitude/Longitude</td><td><?=sprintf('%.6f, %.6f', $report['latitude'], $report['longitude'])?></td></tr>
				<tr><td>Altitude</td><td><?=sprintf('%d ft', $report['altitude'])?></td></tr>
				<tr><td>AGL</td><td><?=sprintf('%d ft', $report['agl'])?></td></tr>
				<?php
				if ($report['fdm'] == 'ufo') {
					?><tr><td>Speed</td><td><?=sprintf('%d kts', $report['equivalent_speed'])?></td></tr><?php
				} else {
					?>
					<tr><td>TAS</td><td><?=sprintf('%d kts', $report['air_speed'])?></td></tr>
					<tr><td>GS</td><td><?=sprintf('%d kts', $report['ground_speed'])?></td></tr>
					<tr><td>Mach</td><td><?=sprintf('%.3f', $report['mach'])?></td></tr>
					<tr><td>Fuel Remaining</td><td><?=sprintf('%.2f%%', $report['fuel'] * 100)?></td></tr>
					<?php
				}
				?>

				<?php
				if ($report['autopilot_active'] == 'ufo') {
				?>

				<tr><td colspan='2'><br/><b>Autopilot</b></td></tr>
				<tr><td>Distance Remaining</td><td><?=sprintf('%.2f nmi', $report['distance_remaining'])?></td></tr>
				<tr><td>Distance Elapsed</td><td><?=sprintf('%.2f nmi', $report['distance_elapsed'])?></td></tr>
				<tr><td>Flight Time</td><td><?=$report['flight_time']?></td></tr>
				<tr><td>Remaining Time</td><td><?=$report['time_remaining']?></td></tr>

				<?php
				}
				?>
				<tr><td></td><td></td></tr>
			</table>
		<?php
	}
}
		?>
	</body>
</html>

