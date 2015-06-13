<?php
function getcputemperature($temp_f = false) {
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
function getgputemperature($temp_f = false) {
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
