var updateReport = func{
	setprop('/fgreport/latitude-deg', getprop('/position/latitude-deg'));
	setprop('/fgreport/longitude-deg', getprop('/position/longitude-deg'));
	setprop('/fgreport/ete-string', getprop('/autopilot/route-manager/ete-string'));
	setprop('/fgreport/flight-time-string', getprop('/autopilot/route-manager/flight-time-string'));
	setprop('/fgreport/distance-remaining-nm', getprop('/autopilot/route-manager/distance-remaining-nm'));
	setprop('/fgreport/total-distance', getprop('/autopilot/route-manager/total-distance'));
	settimer(updateReport, 0.1);
}
_setlistener("/sim/signals/nasal-dir-initialized", func {
	var l = setlistener('/sim/signals/fdm-initialized', func {
		setprop('/fgreport/aircraft', getprop('/sim/description'));
		updateReport();
		removelistener(l);
	});
});
