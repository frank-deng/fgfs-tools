<?php
function get_gpu_temp() {
	try{
		$gpuinfo_xml_text = [];
		exec('nvidia-smi -q -x', $gpuinfo_xml_text);
		$gpuinfo_xml = simplexml_load_string(join("\n", $gpuinfo_xml_text));
		return (float)str_replace(' C', '', $gpuinfo_xml->xpath('/nvidia_smi_log/gpu/temperature')[0]->gpu_temp);
	}catch(Exception $e){
		return null;
	}
}
function get_cpu_temp() {
	try{
		$fp = fopen('/sys/class/thermal/thermal_zone0/temp', 'r');
		$text = fgets($fp);
		fclose($fp);
		return ((float)($text) / 1000.0);
	}catch(Exception $e){
		return null;
	}
}

$sysinfo = [
	'cpu_temp_c' => null,
	'cpu_temp_f' => null,
	'gpu_temp_c' => null,
	'gpu_temp_f' => null,
];

//Get temperature
$sysinfo['cpu_temp_c'] = get_cpu_temp();
if (null != $sysinfo['cpu_temp_c']) {
	$sysinfo['cpu_temp_f'] = $sysinfo['cpu_temp_c'] * 1.8 + 32.0;
}
$sysinfo['gpu_temp_c'] = get_gpu_temp();
if (null != $sysinfo['gpu_temp_c']) {
	$sysinfo['gpu_temp_f'] = $sysinfo['gpu_temp_c'] * 1.8 + 32.0;
}

header('Content-Type: application/json');
$output = json_encode($sysinfo);
if ($output) {
	echo $output;
} else {
	header('HTTP/1.1: 500 Internal Server Error');
	exit;
}

