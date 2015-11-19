<?php
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

require('config.php');

$report_all = Array();
foreach ($config['FG_CONNECTIONS'] as $i => $conn) {
	$report = fgfs_report($conn['host'], $conn['port']);
	if ($report) {
		$report['instance_num'] = $i;
		array_push($report_all, $report);
	}
}
echo json_encode($report_all);

