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

