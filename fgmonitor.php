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

if (isset($_GET['mod']) && $_GET['mod'] == 'report') {
	$report_all = Array();
	foreach ($FG_CONNECTIONS as $i => $conn) {
		$report = fgfs_report($conn['host'], $conn['port']);
		if ($report) {
			$report['instance_num'] = $i;
			array_push($report_all, $report);
		}
	}
	exit(json_encode($report_all));
}

?><!DOCTYPE html>
<html>
	<head>
		<meta name='viewport' id='viewport' content='width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no'/>
		<meta charset='UTF-8'/>
		<title>FlightGear Web Interface</title>
		<style type='text/css'>
body{font-family:sans,arial;}
p{margin:0;padding:0;}
[hidden]{display:none;}
body{font-size:14px;text-shadow:0px 0px 2px #000;color:#FFF;}
#map_container{position:absolute !important;z-index:0;left:0;right:0;top:0;bottom:0;background-color:#000000;}
#info_container{position:absolute;z-index:1000;left:0;top:0;background-color:rgba(0,0,0,0.5);border-radius:4px;cursor:pointer;padding:4px;}
#error_info{position:absolute;left:0;right:0;top:0;text-align:center;z-index:800;padding:4px 4px;}
.we-pm-icon {cursor:pointer;}
#info_container .aircraft{font-weight:bold;}
#info_container table{border-collapse:collapse;}
#info_container th{padding-top:10px;text-align:left;}
#info_container .item-name{padding-right:10px;}
#info_container .crashed{color:#F00;}
		</style>
		<script src="http://frank-deng.gitcafe.io/world-map/v2/api.js"></script>
		<script type='text/javascript'>
function FGReport(params) {
	var _this = this;
	/* Init */
	var earth = new WE.map(params.id_map, {
		sky: false,
		dragging: true,
		panning: true,
		tilting: false,
		atmosphere: true
	});
	WE.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.jpg', {
		subdomains: '1234',
		bounds: [[-89, -180], [89, 180]],
		minZoom: 0,
		maxZoom: 5,
		attribution: 'Tiles Courtesy of MapQuest',
		tms: false
	}).addTo(earth);
	var info_container = document.getElementById(params.id_info);
	var error_container = document.getElementById(params.id_error);
	var instance_selected = 0;
	var markers = undefined;
	this._earth = earth;
	/* End Init */

	var makeReport = function(element, data) {
		var addHeader = function(table, name) {
			var row = document.createElement('tr');
			var header = document.createElement('th');
			header.setAttribute('colspan', '2');
			header.innerHTML = name;
			row.appendChild(header);
			table.appendChild(row);
		}
		var addLine = function(table, name, value) {
			var row = document.createElement('tr');
			var col_title = document.createElement('td');
			var col_value = document.createElement('td');
			col_title.setAttribute('class', 'item-name');
			col_title.innerHTML = name;
			col_value.innerHTML = value;
			row.appendChild(col_title);
			row.appendChild(col_value);
			table.appendChild(row);
		}
		var table = document.createElement('table');

		var aircraft = document.createElement('p');
		aircraft.setAttribute('class', 'aircraft');
		aircraft.innerHTML = data.aircraft;
		element.appendChild(aircraft);

		addHeader(table, 'Time');
		addLine(table, 'Real-world', data.real_world_time);
		addLine(table, 'UTC', data.utc_time);
		addLine(table, 'Local', data.local_time);

		addHeader(table, 'Aircraft');
		addLine(table, 'Latitude / Longitude', data.latitude.toFixed(6) + ', ' + data.longitude.toFixed(6));
		addLine(table, 'Altitude', parseInt(data.altitude) + ' ft');
		addLine(table, 'AGL', parseInt(data.agl) + ' ft');
		if (data.fdm == 'ufo') {
			addLine(table, 'Speed', parseInt(data.equivalent_speed) + ' kts');
		} else {
			addLine(table, 'TAS', parseInt(data.air_speed) + ' kts');
			addLine(table, 'GS', parseInt(data.ground_speed) + ' kts');
			addLine(table, 'Mach', data.mach.toFixed(3));
			addLine(table, 'Fuel Remaining', (data.fuel * 100).toFixed(2) + '%');
		}
		if (data.autopilot_active) {
			addHeader(table, 'Autopilot');
			addLine(table, 'Distance Remaining', data.distance_remaining.toFixed(2) + ' nmi');
			addLine(table, 'Distance Elapsed', data.distance_elapsed.toFixed(2) + ' nmi');
			addLine(table, 'Flight Time', data.flight_time);
			addLine(table, 'Remaining Time', data.time_remaining);
		}

		if (data.paused || data.crashed) {
			var row = document.createElement('tr');
			var header = document.createElement('th');
			header.setAttribute('colspan', '2');

			if (data.crashed) {
				header.setAttribute('class', 'crashed');
				header.innerHTML = '[Crashed]';
			} else if (data.paused) {
				header.innerHTML = '[Paused]';
			}

			row.appendChild(header);
			table.appendChild(row);
		}

		//Apply
		element.appendChild(table);
	}
	var showError = function(text) {
		if (!text) {
			error_container.innerHTML = '';
			error_container.setAttribute('hidden', 'hidden');
			return;
		}
		error_container.removeAttribute('hidden');
		error_container.innerHTML = text;
	}
	var updateMarkers = function(data) {
		if (markers !== undefined) {
			for (var i = 0; i < markers.length; i++) {
				markers[i].removeFrom(earth);
			}
		}
		markers = Array();
		for (var i = 0; i < data.length; i++) {
			markers[i] = WE.marker([data[i].latitude, data[i].longitude]).addTo(earth);
			markers[i].element.setAttribute('class', 'marker');
			markers[i].element.setAttribute('idx', i);
			markers[i].element.onclick = function(){
				info_container.innerHTML = '';
				_this.update(this.getAttribute('idx'), function(){
					info_container.removeAttribute('hidden');
				});
			}
		}
	}

	this.update = function(target_instance, callback) {
		var xmlhttp = new XMLHttpRequest();
		showError();
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200){
					showError();
					var fgreport_data = JSON.parse(xmlhttp.responseText);
					updateMarkers(fgreport_data);
					if (fgreport_data.length == 0) {
						showError('No FlightGear Instance is running.');
						info_container.setAttribute('hidden', 'hidden');
						return;
					}

					instance_selected = (undefined !== target_instance ? target_instance : instance_selected);
					if (undefined === fgreport_data[instance_selected]) {
						instance_selected = 0;
					}

					info_container.innerHTML = '';
					makeReport(info_container, fgreport_data[instance_selected]);

					if (callback) {
						callback.apply(_this, [fgreport_data, instance_selected]);
					}
				} else {
					showError('Failed to fetch data.');
					info_container.setAttribute('hidden', 'hidden');
				}
			}
		}
		xmlhttp.open("POST", params.fgreport_url, true);
		xmlhttp.send(null);
	}
}

window.onload = function(){
	var fgReport = new FGReport({
		id_map: 'map_container',
		id_info: 'info_container',
		id_error: 'error_info',
		fgreport_url: 'fgmonitor.php?mod=report',
	});

	document.getElementById('info_container').onclick = function(e){
		this.innerHTML = '';
		this.setAttribute('hidden', 'hidden');
	}

	//Press 'r' to refresh
	var kbdHandler = function(e) {
		switch (e.keyCode) {
			case 32:	//Space: Refresh
			case 82:	//R: Refresh
				fgReport.update();
			break;
		}
	}
	if (window.onkeydown) {
		window.onkeydown = kbdHandler;
	} else {
		document.onkeydown = kbdHandler;
	}

	//Shake to refresh
	var ts_last = 0;
	var accel_last = {x:undefined, y:undefined, z:undefined};
	var motion_interval = 1000;
	var SHAKE_THRESHOLD = 3.0;
	if (window.DeviceMotionEvent) {  
		window.addEventListener('devicemotion', function(e){
			var ts_now = new Date().getTime();
			if ((ts_now - ts_last) > motion_interval) {
				ts_last = ts_now;
				var accel = e.accelerationIncludingGravity;

				var dx = (accel_last.x === undefined ? 0 : Math.abs(accel.x - accel_last.x));
				var dy = (accel_last.y === undefined ? 0 : Math.abs(accel.y - accel_last.y));
				var dz = (accel_last.z === undefined ? 0 : Math.abs(accel.z - accel_last.z));
				accel_last.x = accel.x; accel_last.y = accel.y; accel_last.z = accel.z;
				
				if (dx > SHAKE_THRESHOLD || dy > SHAKE_THRESHOLD || dz > SHAKE_THRESHOLD) {
					fgReport.update();
				}
			}
		}, false);  
	}

	fgReport.update(0, function(data, i){
		this._earth.setView([data[i].latitude, data[i].longitude]);
	});
	setInterval(fgReport.update, 15000);
}

		</script>
	</head>
	<body>
		<div id='info_container' hidden='hidden'></div>
		<div id='map_container'></div>
		<div id='error_info' hidden='hidden'></div>
	</body>
</html>

