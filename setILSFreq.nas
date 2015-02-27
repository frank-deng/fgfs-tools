_setlistener("/sim/signals/nasal-dir-initialized", func {
	setlistener('/autopilot/route-manager/active', func {
		var dest_apt = getprop('/autopilot/route-manager/destination/airport');
		var dest_rwy = getprop('/autopilot/route-manager/destination/runway');
		var rwy = airportinfo(dest_apt).runways[dest_rwy];
		if (rwy != nil) {
			var ils = rwy.ils;
			if (ils != nil) {
				var ils_freq = ils.frequency/100;
				setprop('/instrumentation/nav/frequencies/selected-mhz', ils_freq);
				setprop('/instrumentation/nav[1]/frequencies/selected-mhz', ils_freq);
			}
		}
	});
}, 0, 0);

