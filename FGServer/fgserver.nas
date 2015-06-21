var refresh_fgreport = func {
	var path = '/command/fgreport/';
	setprop(path ~ 'aircraft', getprop('/sim/description'));
	setprop(path ~ 'fdm', getprop('/sim/flight-model'));
	setprop(path ~ 'real_world_time', getprop('/sim/time/real/string'));
	setprop(path ~ 'utc_time', getprop('/sim/time/real/string'));
	setprop(path ~ 'local_time', getprop('/instrumentation/clock/local-short-string'));
	setprop(path ~ 'longitude', getprop('/position/longitude-deg'));
	setprop(path ~ 'latitude', getprop('/position/latitude-deg'));
	setprop(path ~ 'altitude', getprop('/position/altitude-ft'));
	setprop(path ~ 'agl', getprop('/position/altitude-agl-ft'));
	setprop(path ~ 'heading', getprop('/orientation/heading-deg'));
	setprop(path ~ 'vertical_speed', getprop('/velocities/vertical-speed-fps') * 60.0);
	props.globals.getNode(path ~ 'autopilot_active').setBoolValue(getprop('/autopilot/route-manager/active'));
	props.globals.getNode(path ~ 'crashed').setBoolValue(getprop('/sim/crashed'));
	props.globals.getNode(path ~ 'paused').setBoolValue(getprop('/sim/freeze/clock') and getprop('/sim/freeze/master'));
	if (getprop('/sim/flight-model') == 'ufo') {
		setprop(path ~ 'equivalent_speed', getprop('/velocities/equivalent-kt'));
	} else {
		setprop(path ~ 'indicated_air_speed', getprop('/instrumentation/airspeed-indicator/indicated-speed-kt'));
		setprop(path ~ 'mach', getprop('/instrumentation/airspeed-indicator/indicated-mach'));
		setprop(path ~ 'air_speed', getprop('/instrumentation/airspeed-indicator/true-speed-kt'));
		setprop(path ~ 'ground_speed', getprop('/velocities/groundspeed-kt'));
		setprop(path ~ 'fuel', getprop('/consumables/fuel/total-fuel-norm'));
	}
	if (getprop('/autopilot/route-manager/active')) {
		var total_distance = getprop('/autopilot/route-manager/total-distance');
		var distance_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
		setprop(path ~ 'total_distance', total_distance);
		setprop(path ~ 'distance_remaining', distance_remaining);
		setprop(path ~ 'distance_elapsed', total_distance - distance_remaining);
		setprop(path ~ 'flight_time', getprop('/autopilot/route-manager/flight-time-string'));
		setprop(path ~ 'time_remaining', getprop('/autopilot/route-manager/ete-string'));
	}
	setprop(path ~ 'fps_limit', getprop('/sim/frame-rate-throttle-hz'));
	setprop(path ~ 'frame_rate', getprop('/sim/frame-rate'));
	setprop(path ~ 'frame_latency', getprop('/sim/frame-latency-max-ms'));
}
var refresh_fgreport_loop = func {
	call(refresh_fgreport, [], var err = []);
	settimer(refresh_fgreport_loop, 0);
}
_setlistener("/sim/signals/nasal-dir-initialized", func {
	props.globals.getNode('/command').addChild('fgreport');
	props.globals.getNode('/command/fgreport').addChild('autopilot_active');
	props.globals.getNode('/command/fgreport').addChild('crashed');
	props.globals.getNode('/command/fgreport').addChild('paused');
	refresh_fgreport_loop();
});

