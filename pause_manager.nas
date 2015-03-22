var pause_manager = func{
	if (!getprop('/autopilot/settings/pause-manager-enabled')) {
		return;
	} else if (!getprop('/autopilot/route-manager/active')){
		screen.log.write('Sorry, you haven\'t activated your route yet.', 1, 0, 0);
		setprop('/autopilot/settings/pause-manager-enabled', '0');
		return;
	}

	var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
	var dist_pause = getprop('/autopilot/settings/pause-manager-distance');
	if (dist_remaining <= dist_pause){
		setprop('/sim/freeze/clock', 1);
		setprop('/sim/freeze/master', 1);
		setprop('/autopilot/settings/pause-manager-enabled', 0);
	}
}
var pause_manager_mainloop = func() {
	pause_manager();
	settimer(pause_manager_mainloop, 0);
}
_setlistener("/sim/signals/nasal-dir-initialized", func() {
	setprop('/autopilot/settings/pause-manager-distance', '20');
	setprop('/autopilot/settings/pause-manager-enabled', '0');
	pause_manager_mainloop();
});

