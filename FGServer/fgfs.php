<?php
require('FGTelnet.php');
require('config.php');

//No post data received
if (!isset($_POST['commands']) or !isset($_POST['instance'])) {
	header('HTTP/1.1: 400 Bad Request');
	exit;
}

//Get instance number
$instance = (int)($_POST['instance']);
if ($instance < 0 or $instance > 9) {
	header('HTTP/1.1: 404 Not Found');
	exit;
}

//Get connection and process commands.
try {
	$fg = new FGTelnet(FG_HOST, FG_PORT_BASE + $instance);
} catch (SocketException $e) {
	header('HTTP/1.1: 404 Not Found');
	exit;
}

//Process commands, and prepare data for 'get' command
function get_result($fg, $prop, $type) {
	switch($type) {
		case 'bool':
			return $fg->getBool($prop);
		break;
		case 'int':
		case 'integer':
			return $fg->getInt($prop);
		break;
		case 'float':
		case 'double':
			return $fg->getFloat($prop);
		break;
		default:
			return $fg->get($prop);
		break;
	}
}
$result = [];
try {
	foreach($_POST['commands'] as $cmd) {
		switch($cmd['command']) {
			case 'get':
				$key = isset($cmd['alias']) ? $cmd['alias'] : $cmd['prop'];
				$result[$key] = get_result($fg, $cmd['prop'], $cmd['type']);
			break;
			case 'set':
				$fg->set($cmd['prop'], $cmd['value']);
			break;
			case 'run':
				$fg->run($cmd['param']);
			break;
		}
	}
	$fg->close();
} catch (SocketException $e) {
	header('HTTP/1.1: 500 Internal Server Error');
	exit;
}

//Output json data for 'get' command
header('Content-Type: application/json');
$output = json_encode($result);
if ($output) {
	echo $output;
} else {
	header('HTTP/1.1: 500 Internal Server Error');
	exit;
}

