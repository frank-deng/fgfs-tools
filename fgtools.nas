# =============
# Pause Manager
# =============
var pause_manager = func
{
	if (getprop('/autopilot/pausemgr-dist') > 0
		and getprop('/autopilot/route-manager/active'))
	{
		var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
		var dist_pause = getprop('/autopilot/pausemgr-dist');
		var paused = 0;

		if (dist_remaining <= dist_pause) {
			setprop('/autopilot/pausemgr-dist', -1);
			props.globals.getNode('/sim/freeze/clock').setBoolValue(1);
			props.globals.getNode('/sim/freeze/master').setBoolValue(1);
			paused = 1;
		} else if (getprop('/sim/crashed')) {
			#Pause simulation if aircraft crashed half way
			setprop('/autopilot/pausemgr-dist', -1);
			props.globals.getNode('/sim/freeze/clock').setBoolValue(1);
			props.globals.getNode('/sim/freeze/master').setBoolValue(1);
			paused = 1;
		}

		if (paused) {
			#Slow down fps to reduce overhead
			if (nil == getprop('/sim/gui/frame-rate-throttled')) {
				props.globals.getNode('/sim/gui').addChild('frame-rate-throttled');
			}
			props.globals.getNode('/sim/gui/frame-rate-throttled').setBoolValue(1);
			setprop('/sim/frame-rate-throttle-hz', 10);

			#Resume fps when sim resumed
			var t_resume_fps = setlistener('/sim/freeze/master', func {
				if (!getprop('/sim/freeze/master')) {
					setprop('/sim/frame-rate-throttle-hz', 60);
				}
				removelistener(t_resume_fps);
			});
		}
	}

	settimer(pause_manager, 0.1);
}
_setlistener('/sim/signals/nasal-dir-initialized', func() {
	if (nil == getprop('/autopilot/pausemgr-dist')) {
		setprop('/autopilot/pausemgr-dist', -1);
	}
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

