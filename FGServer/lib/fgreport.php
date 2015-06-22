<?php
require_once('config.php');

function cpu_temperature($temp_f = false) {
	try{
		$fp = fopen('/sys/class/thermal/thermal_zone0/temp', 'r');
		$text = fgets($fp);
		fclose($fp);

		$temperature = ((float)($text) / 1000.0);
		if ($temp_f) {
			$temperature = $temperature * 1.8 + 32.0;
		}
		return $temperature;
	}catch(Exception $e){
		return null;
	}
}
function gpu_temperature($temp_f = false) {
	try{
		$gpuinfo_xml_text = [];
		exec('nvidia-smi -q -x', $gpuinfo_xml_text);
		$gpuinfo_xml = simplexml_load_string(join("\n", $gpuinfo_xml_text));

		$temperature = (float)str_replace(' C', '', $gpuinfo_xml->xpath('/nvidia_smi_log/gpu/temperature')[0]->gpu_temp);
		if ($temp_f) {
			$temperature = $temperature * 1.8 + 32.0;
		}
		return $temperature;
	}catch(Exception $e){
		return null;
	}
}
function fgfs_report($instance) {
	$conn = curl_init();
	$host = explode('|', FG_HOSTS)[$instance];
	$url = $host.'/json/command/fgreport';
	curl_setopt($conn, CURLOPT_URL, $url);
	curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($conn, CURLOPT_HEADER, 0);
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

