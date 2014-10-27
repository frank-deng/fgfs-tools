var mod = func(n, m) {
    var x = n - m * int(n/m);      # int() truncates to zero, not -Inf
    return x < 0 ? x + abs(m) : x; # ...so must handle negative n's
}

fgreport_generate = func{
	var report_text = "\n";

	report_text = report_text ~ getprop('/sim/description') ~ "\n\n";

	report_text = report_text ~ sprintf("Real time: %4d-%02d-%02d %02d:%02d:%02d\n",
					getprop('/sim/time/real/year'),
					getprop('/sim/time/real/month'),
					getprop('/sim/time/real/day'),
					getprop('/sim/time/real/hour'),
					getprop('/sim/time/real/minute'),
					getprop('/sim/time/real/second'));

	report_text = report_text ~ sprintf("UTC time: %4d-%02d-%02d %02d:%02d:%02d\n",
					getprop('/sim/time/utc/year'),
					getprop('/sim/time/utc/month'),
					getprop('/sim/time/utc/day'),
					getprop('/sim/time/utc/hour'),
					getprop('/sim/time/utc/minute'),
					getprop('/sim/time/utc/second'));

	local_hour = getprop('/sim/time/utc/hour') + getprop('/sim/time/local-offset') / 3600;
	if (local_hour < 0) {
		local_hour = 24 + local_hour;
	}
	report_text = report_text ~ sprintf("Local time: %02d:%02d:%02d\n\n",
					local_hour,
					getprop('/sim/time/utc/minute'),
					getprop('/sim/time/utc/second'));

	var latitude = getprop('/position/latitude-deg');
	var longitude = getprop('/position/longitude-deg');
	report_text = report_text ~ sprintf("Latitude: %.5f%s\n", latitude, latitude < 0 ? 'S' : 'N');
	report_text = report_text ~ sprintf("Longitude: %.5f%s\n", longitude, longitude < 0 ? 'W' : 'E');
	report_text = report_text ~ sprintf("Altitude: %dft\n", getprop('/position/altitude-ft'));
	var velocity = getprop('/velocities/groundspeed-kt');
	if ('ufo' == getprop('/sim/flight-model')) {
		var velocity = getprop('/velocities/equivalent-kt');
	}
	report_text = report_text ~ sprintf("Velocity: %dkts\n\n", velocity);
	report_text = report_text ~ sprintf("Heading: %dft\n", getprop('/position/heading'));

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

