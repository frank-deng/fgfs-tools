<?php
function getfgreport($host, $port, $instance) {
	$result = Array();
	try {
		$fg = new FGTelnet($host, $port + $instance);

		$result = Array(
			'fdm' => $fg->get('/sim/flight-model'),
			'aircraft' => $fg->get('/sim/description'),
			'real_world_time' => $fg->get('/sim/time/real/string'),
			'utc_time' => $fg->get('/sim/time/real/string'),
			'local_time' => $fg->get('/instrumentation/clock/local-short-string'),
			'latitude' => $fg->getFloat('/position/latitude-deg'),
			'longitude' => $fg->getFloat('/position/longitude-deg'),
			'altitude' => $fg->getInt('/position/altitude-ft'),
			'agl' => $fg->getInt('/position/altitude-agl-ft'),
			'heading' => $fg->getFloat('/orientation/heading-deg'),
			'vertical_speed' => $fg->getFloat('/velocities/vertical-speed-fps'),
			'autopilot_active' => $fg->getBool('/autopilot/route-manager/active'),
			'crashed' => $fg->getBool('/sim/crashed'),
			'paused' => $fg->getBool('/sim/freeze/clock') && $fg->getBool('/sim/freeze/master'),
		);

		//Speed and Fuel
		if ($result['fdm'] == 'ufo') {
			$result['equivalent_speed'] = $fg->getInt('/velocities/equivalent-kt');
		} else {
			$result['indicated_air_speed'] = $fg->getInt('/instrumentation/airspeed-indicator/indicated-speed-kt');
			$result['mach'] = $fg->getFloat('/instrumentation/airspeed-indicator/indicated-mach');
			$result['air_speed'] = $fg->getInt('/instrumentation/airspeed-indicator/true-speed-kt');
			$result['ground_speed'] = $fg->getInt('/velocities/groundspeed-kt');
			$result['fuel'] = $fg->getFloat('/consumables/fuel/total-fuel-norm');
		}

		//Autopilot
		if ($result['autopilot_active']) {
			$result['total_distance'] = $fg->getFloat('/autopilot/route-manager/total-distance');
			$result['distance_remaining'] = $fg->getFloat('/autopilot/route-manager/distance-remaining-nm');
			$result['distance_elapsed'] = $result['total_distance'] - $result['distance_remaining'];
			$result['flight_time'] = $fg->get('/autopilot/route-manager/flight-time-string');
			$result['time_remaining'] = $fg->get('/autopilot/route-manager/ete-string');
		}

	} catch (SocketException $e) {
		$result = null;
	}
	return $result;
}

