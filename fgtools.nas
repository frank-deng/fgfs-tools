# =============
# Pause Manager
# =============
var pause_manager = func
{
	if (getprop('/autopilot/settings/pause-manager-enabled')
		and getprop('/autopilot/route-manager/active'))
	{
		var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
		var dist_pause = getprop('/autopilot/settings/pause-manager-distance');
		if (dist_remaining <= dist_pause)
		{
			props.globals.getNode('/autopilot/settings/pause-manager-enabled').setBoolValue(0);
			props.globals.getNode('/sim/freeze/clock').setBoolValue(1);
			props.globals.getNode('/sim/freeze/master').setBoolValue(1);
		}
	}

	settimer(pause_manager, 0);
}
_setlistener('/sim/signals/nasal-dir-initialized', func() {
	setprop('/autopilot/settings/pause-manager-distance', 20);
	props.globals.getNode('/autopilot/settings').addChild('pause-manager-enabled').setBoolValue(1);
	pause_manager();
});

# ======================================================
# Automatically activate Route Manager and ILS Frequency
# ======================================================
_setlistener("/sim/signals/nasal-dir-initialized", func {
	setlistener('/autopilot/route-manager/active', func {
		if (!getprop('/autopilot/route-manager/active')) {
			return;
		}
		var dest_apt = getprop('/autopilot/route-manager/destination/airport');
		var dest_rwy = getprop('/autopilot/route-manager/destination/runway');
		var aptinfo = airportinfo(dest_apt);
		if (aptinfo == nil) {
			return;
		}
		var rwy = aptinfo.runways[dest_rwy];
		if (rwy == nil) {
			return;
		}
		var ils = rwy.ils;
		if (ils == nil) {
			return;
		}
		var ils_freq = ils.frequency/100;
		setprop('/instrumentation/nav/frequencies/selected-mhz', ils_freq);
		setprop('/instrumentation/nav[1]/frequencies/selected-mhz', ils_freq);
	});

	var l_set_route = setlistener('/sim/signals/fdm-initialized', func {
		if (getprop('/autopilot/route-manager/file-path')) {
			setprop('/autopilot/route-manager/input', '@LOAD');
			setprop('/autopilot/route-manager/departure/sid', 'DEFAULT');
			setprop('/autopilot/route-manager/destination/approach', 'DEFAULT');
			setprop('/autopilot/route-manager/input', '@ACTIVATE');
			setprop('/autopilot/route-manager/input', '@JUMP0');
		}
		removelistener(l_set_route);
	});
}, 0, 0);

# ===========================
# Print report for FlightGear
# ===========================
var mod = func(n, m) {
    var x = n - m * int(n/m);      # int() truncates to zero, not -Inf
    return x < 0 ? x + abs(m) : x; # ...so must handle negative n's
}
fgreport_generate = func{
	var report_text = "\n";

	report_text = report_text ~ getprop('/sim/description') ~ "\n\n";

	report_text = report_text ~ sprintf(
		"Real-world time: %4d-%02d-%02d %02d:%02d:%02d\n",
		getprop('/sim/time/real/year'),
		getprop('/sim/time/real/month'),
		getprop('/sim/time/real/day'),
		getprop('/sim/time/real/hour'),
		getprop('/sim/time/real/minute'),
		getprop('/sim/time/real/second')
	);

	report_text = report_text ~ sprintf(
		"UTC time: %4d-%02d-%02d %02d:%02d:%02d\n",
		getprop('/sim/time/utc/year'),
		getprop('/sim/time/utc/month'),
		getprop('/sim/time/utc/day'),
		getprop('/sim/time/utc/hour'),
		getprop('/sim/time/utc/minute'),
		getprop('/sim/time/utc/second')
	);

	local_hour = getprop('/sim/time/utc/hour')
		+ getprop('/sim/time/local-offset') / 3600;
	if (local_hour < 0) {
		local_hour = 24 + local_hour;
	} else if (local_hour >= 24) {
		local_hour = local_hour - 24;
	}

	report_text = report_text ~ sprintf(
		"Local time: %02d:%02d:%02d\n\n",
		local_hour,
		getprop('/sim/time/utc/minute'),
		getprop('/sim/time/utc/second')
	);

	var latitude = getprop('/position/latitude-deg');
	var longitude = getprop('/position/longitude-deg');
	report_text = report_text ~ sprintf("Position: %.5f%s %5f%s\n", abs(latitude), latitude < 0 ? 'S' : 'N', abs(longitude), longitude < 0 ? 'W' : 'E');
	report_text = report_text ~ sprintf("Altitude: %dft\n", getprop('/position/altitude-ft'));
	var velocity = getprop('/velocities/groundspeed-kt');
	if ('ufo' == getprop('/sim/flight-model')) {
		var velocity = getprop('/velocities/equivalent-kt');
	}
	report_text = report_text ~ sprintf("Velocity: %dkts\n\n", velocity);

	var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
	var dist_total = getprop('/autopilot/route-manager/total-distance');
	var dist_elapsed = dist_total - dist_remaining;
	time_elapsed = getprop('/autopilot/route-manager/flight-time');
	time_remaining = getprop('/autopilot/route-manager/ete');
	report_text = report_text ~ sprintf("Total Distance: %.1fnmi\n", dist_total);
	report_text = report_text ~ sprintf("Total Remaining Distance: %.1fnmi\n", dist_remaining);
	report_text = report_text ~ sprintf("Total Elapsed Distance: %.1fnmi\n", dist_elapsed);
	report_text = report_text ~ sprintf("Flight Time: %02d:%02d:%02d\n",
					int(time_elapsed / 3600),
					mod(int(time_elapsed / 60), 60),
					mod(time_elapsed, 60));
	report_text = report_text ~ sprintf("Total Time Remining: %02d:%02d:%02d\n",
					int(time_remaining / 3600),
					mod(int(time_remaining / 60), 60),
					mod(time_remaining, 60));

	if ('ufo' != getprop('/sim/flight-model')) {
		report_text = report_text ~ sprintf("\nFuel Remaining: %.2f pounds / %.2f gallons / %.1f%%\n"
			,getprop('/consumables/fuel/total-fuel-lbs')
			,getprop('/consumables/fuel/total-fuel-gal_us')
			,getprop('/consumables/fuel/total-fuel-norm') * 100
		);
	}

	if (getprop('/sim/crashed')) {
		report_text = report_text ~ "\nAircraft Crashed!!!\n";
	}

	if (getprop('/sim/freeze/clock') and getprop('/sim/freeze/master')) {
		report_text = report_text ~ "\nSimulation paused.\n";
	}

	setprop('/sim/fgreport/text', report_text);
}

_setlistener("/sim/signals/nasal-dir-initialized", func {
	setprop('/sim/signals/fgreport', '');
	setprop('/sim/fgreport/text', '');
	setlistener('/sim/signals/fgreport', func {
		if (getprop('/sim/signals/fgreport')) {
			fgreport_generate();
			setprop('/sim/signals/fgreport', '');
		}
	});
}, 0, 0);

