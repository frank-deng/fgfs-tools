var updateReport = func{
	setprop('/fgreport/latitude-deg', getprop('/position/latitude-deg'));
	setprop('/fgreport/longitude-deg', getprop('/position/longitude-deg'));
	setprop('/fgreport/ete-string', getprop('/autopilot/route-manager/ete-string'));
	if(getprop('/sim/time/local-day-string')){
		setprop('/fgreport/local-time-string', getprop('/sim/time/local-day-string'));
	}
	setprop('/fgreport/gmt-string', getprop('/sim/time/gmt-string'));
	setprop('/fgreport/flight-time-string', getprop('/autopilot/route-manager/flight-time-string'));
	setprop('/fgreport/distance-remaining-nm', getprop('/autopilot/route-manager/distance-remaining-nm'));
	setprop('/fgreport/total-distance', getprop('/autopilot/route-manager/total-distance'));

	setprop('/fgreport/heading-deg', getprop('/orientation/heading-deg'));
	setprop('/fgreport/altitude-ft', getprop('/position/altitude-ft'));
	setprop('/fgreport/altitude-agl-ft', getprop('/position/altitude-agl-ft'));
	setprop('/fgreport/vertical-speed-fps', getprop('/velocities/vertical-speed-fps'));

	if (getprop('/sim/flight-model') == 'ufo') {
		setprop('/fgreport/speed-kt', getprop('/velocities/equivalent-kt'));
	} else {
		setprop('/fgreport/mach', getprop('/velocities/mach'));
		setprop('/fgreport/airspeed-kt', getprop('/velocities/airspeed-kt'));
		setprop('/fgreport/groundspeed-kt', getprop('/velocities/groundspeed-kt'));
		setprop('/fgreport/remain-fuel', getprop('/consumables/fuel/total-fuel-norm'));
	}

	settimer(updateReport, 0.1);
}
_setlistener("/sim/signals/nasal-dir-initialized", func {
	var l = setlistener('/sim/signals/fdm-initialized', func {
		setprop('/fgreport/aircraft', getprop('/sim/description'));
		setprop('/fgreport/paused', 0);
		setprop('/fgreport/crashed', 0);
		setprop('/fgreport/flight-model', getprop('/sim/flight-model'));
		if (getprop('/sim/flight-model') != 'ufo') {
			setprop('/fgreport/initial-fuel', getprop('/consumables/fuel/total-fuel-norm'));
		}

		setlistener('/sim/freeze/master', func {
			props.globals.getNode('/fgreport/paused').setBoolValue(getprop('/sim/freeze/master'));
		});
		setlistener('/sim/crashed', func {
			props.globals.getNode('/fgreport/crashed').setBoolValue(getprop('/sim/crashed'));
		});
		updateReport();
		removelistener(l);
	});
});

var mod = func(n, m) {
    var x = n - m * int(n/m);      # int() truncates to zero, not -Inf
    return x < 0 ? x + abs(m) : x; # ...so must handle negative n's
}
var update_time_string = func(){
	#/autopilot/route-manager/ete-string
	var time_remaining = getprop('/autopilot/route-manager/ete');
	if (time_remaining <= 2147483647) {
		setprop('/autopilot/route-manager/ete-string'
			,sprintf('%02d:%02d:%02d'
				,int(time_remaining / 3600)
				,mod(int(time_remaining / 60), 60)
				,mod(time_remaining, 60)
			)
		);
	} else {
		setprop('/autopilot/route-manager/ete-string', '00:00:00');
	}

	#/autopilot/route-manager/flight-time-string
	var time_elapsed = getprop('/autopilot/route-manager/flight-time');
	setprop('/autopilot/route-manager/flight-time-string',
		sprintf(
			'%02d:%02d:%02d'
			,int(time_elapsed / 3600)
			,mod(int(time_elapsed / 60), 60)
			,mod(time_elapsed, 60)
		)
	);

	var local_time = getprop('/sim/time/local-day-seconds');
	if(local_time){
		setprop('/sim/time/local-day-string',
			sprintf(
				'%02d:%02d:%02d'
				,int(local_time / 3600)
				,mod(int(local_time / 60), 60)
				,mod(local_time, 60)
			)
		);
	}

	settimer(update_time_string, 0.1);
}
_setlistener("/sim/signals/nasal-dir-initialized", func {
	update_time_string();
});

