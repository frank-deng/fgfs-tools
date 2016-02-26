# =============
# fgreport.nas
# =============

var path = '/fgreport/';
var refresh_fgreport = func {
	setprop(path ~ 'real_world_time', sprintf(
		'%4d-%02d-%02dT%02d:%02d:%02d',
		getprop('/sim/time/real/year'),
		getprop('/sim/time/real/month'),
		getprop('/sim/time/real/day'),
		getprop('/sim/time/real/hour'),
		getprop('/sim/time/real/minute'),
		getprop('/sim/time/real/second')
	));
	setprop(path ~ 'utc_time', getprop('/sim/time/gmt'));
	setprop(path ~ 'local_time', getprop('/instrumentation/clock/local-short-string'));
	setprop(path ~ 'longitude', getprop('/position/longitude-deg'));
	setprop(path ~ 'latitude', getprop('/position/latitude-deg'));
	setprop(path ~ 'altitude', getprop('/position/altitude-ft'));
	setprop(path ~ 'agl', getprop('/position/altitude-agl-ft'));
	setprop(path ~ 'heading', getprop('/orientation/heading-deg'));
	setprop(path ~ 'vertical_speed', getprop('/velocities/vertical-speed-fps') * 60.0);
	props.globals.getNode(path ~ 'autopilot_active').setBoolValue(getprop('/autopilot/route-manager/active'));
	props.globals.getNode(path ~ 'crashed').setBoolValue(getprop('/sim/crashed'));
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

	props.globals.getNode(path ~ 'on-ground').setBoolValue(getprop('/gear/on-ground'));
}
var refresh_fgreport_loop = func {
	call(refresh_fgreport, [], var err = []);
	settimer(refresh_fgreport_loop, 0);
}
_setlistener("/sim/signals/nasal-dir-initialized", func {
	props.globals.getNode('/').addChild('fgreport');
	props.globals.getNode('/fgreport').addChild('autopilot_active');
	props.globals.getNode('/fgreport').addChild('crashed');
	props.globals.getNode('/fgreport').addChild('paused');
	props.globals.getNode(path ~ 'paused').setBoolValue(0);
	props.globals.getNode('/fgreport').addChild('on-ground');
	setprop(path ~ 'aircraft', getprop('/sim/description'));
	setprop(path ~ 'fdm', getprop('/sim/flight-model'));
	setprop(path ~ 'session', getprop('/sim/session'));

	setlistener("/sim/freeze/master", func {
		props.globals.getNode(path ~ 'paused').setBoolValue(getprop('/sim/freeze/master'));
	});

	props.globals.getNode('/').addChild('fgmonitor');
	props.globals.getNode('/fgmonitor').addChild('status');

	refresh_fgreport_loop();
});

