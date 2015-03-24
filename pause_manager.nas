var pause_manager = func{
	if (getprop('/autopilot/settings/pause-manager-enabled')) {
		if (!getprop('/autopilot/route-manager/active')){
			screen.log.write('Sorry, you haven\'t activated your route yet.', 1, 0, 0);
			setprop('/autopilot/settings/pause-manager-enabled', 0);
		} else {
			var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
			var dist_pause = getprop('/autopilot/settings/pause-manager-distance');
			if (dist_remaining <= dist_pause){
				setprop('/sim/freeze/clock', 1);
				setprop('/sim/freeze/master', 1);
				setprop('/autopilot/settings/pause-manager-enabled', 0);
			}
		}
	}

	settimer(pause_manager, 0);
}
_setlistener("/sim/signals/nasal-dir-initialized", func() {
	setprop('/autopilot/settings/pause-manager-distance', 20);
	setprop('/autopilot/settings/pause-manager-enabled', 0);
	pause_manager();

	setlistener('/autopilot/route-manager/active', func {
		if (getprop('/autopilot/route-manager/active')) {
			setprop('/autopilot/settings/pause-manager-enabled', 1);
		} else {
			setprop('/autopilot/settings/pause-manager-enabled', 0);
		}
	});
});

