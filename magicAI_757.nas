var abs = func(n) { n < 0 ? -n : n }

var flaps_manager = func {
	if (!getprop('/gear/on-ground') and getprop('/position/altitude-agl-ft') > 100) {
		var speed = getprop('/velocities/airspeed-kt');
		var flaps = getprop('/controls/flight/flaps');
		if (240 < speed and flaps != 0) {
			setprop('/controls/flight/flaps', 0);
		} else if (230 < speed and speed < 240 and flaps != 0.033) {
			setprop('/controls/flight/flaps', 0.033);
		} else if (205 < speed and speed < 230 and flaps != 0.166) {
			setprop('/controls/flight/flaps', 0.166);
		} else if (180 < speed and speed < 205 and flaps != 0.500) {
			setprop('/controls/flight/flaps', 0.500);
		} else if (170 < speed and speed < 180 and flaps != 0.666) {
			setprop('/controls/flight/flaps', 0.666);
		} else if (speed < 170 and flaps != 1.000) {
			setprop('/controls/flight/flaps', 1.000);
		}
	}
	settimer(flaps_manager, 1);
}
var magicAI_start = func {
	setprop('sim/model/start-idling', 1);
	flaps_manager();
	settimer(func(){
		setprop('/controls/lighting/landing-lights', 0);
		setprop('/controls/lighting/landing-lights[1]', 0);
		setprop('/controls/lighting/logo-lights', 0);
		setprop('/controls/lighting/mcp-panel-norm', 1);
		setprop('/controls/lighting/cockpit-panel-norm', 1);

		setprop('/controls/flight/flaps', 0.667);

		setprop('/autopilot/settings/target-speed-kt', 270);
		setprop('/instrumentation/afds/inputs/at-armed', 1);
		setprop('/instrumentation/afds/inputs/lateral-index', 3);
		setprop('/instrumentation/afds/inputs/vertical-index', 5);

		#Begin takeoff
		settimer(func(){
			setprop('/controls/gear/brake-parking', 0);
			magicAI_takeoff();
		}, 20);
	}, 2);
}
var magicAI_takeoff = func {
	if (!getprop('/gear/on-ground') and getprop('/position/altitude-agl-ft') > 40) {
		setprop('/controls/gear/gear-down', 0);
		setprop('/controls/lighting/taxi-lights', 0);
		setprop('/controls/lighting/logo-lights', 0);
		setprop('/controls/lighting/wing-lights', 0);
		magicAI_climb();return;
	}
	settimer(magicAI_takeoff, 1);
}
var magicAI_climb = func {
	if (!getprop('/gear/on-ground') and getprop('/position/altitude-agl-ft') > 210) {
		setprop('/instrumentation/afds/inputs/autothrottle-index', 5);
		setprop('/instrumentation/afds/inputs/AP', 1);
		setprop('/controls/flight/rudder', 0);
		magicAI_adjust_elev();
		magicAI_cruise();return;
	}
	settimer(magicAI_climb, 1);
}
var magicAI_adjust_elev = func{
	if(getprop('/position/altitude-agl-ft')>1000){
		var elev=getprop('/controls/flight/elevator');
		if(abs(elev)<0.02){
			setprop('/controls/flight/elevator',0);
			return;
		}
		if(elev>0){
			setprop('/controls/flight/elevator',elev-0.02);
		} else if(elev<0){
			setprop('/controls/flight/elevator',elev+0.02);
		}
	}
	settimer(magicAI_adjust_elev, 200);
}
var magicAI_cruise = func {
	var route_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
	if (route_remaining < 22) {
		setprop('/autopilot/settings/vertical-speed-fpm', -1000);
		setprop('/autopilot/settings/target-speed-kt', 200);
		setprop('/instrumentation/afds/inputs/vertical-index', 1);
		setprop('/autopilot/settings/alt-display-ft', getprop('/autopilot/settings/target-altitude-ft') - 2000);
		setprop('/autopilot/settings/altitude-setting-ft', getprop('/autopilot/settings/target-altitude-ft') - 2000);
		setprop('/controls/flight/speedbrake-lever', 1);
		settimer(func(){
			setprop('/instrumentation/afds/inputs/loc-armed', 1);
			setprop('/instrumentation/afds/inputs/gs-armed', 1);
			magicAI_landing();
		}, 0.5);
		return;
	}
	settimer(magicAI_cruise, 1);
}
var magicAI_landing = func {
	if (getprop('/instrumentation/afds/inputs/vertical-index') == 6) {
		setprop('/controls/gear/gear-down', 1);
		setprop('/autopilot/settings/target-speed-kt', 157);
		setprop('/controls/lighting/taxi-lights', 1);
		setprop('/controls/lighting/logo-lights', 1);
		setprop('/controls/lighting/wing-lights', 1);
		setprop('/controls/lighting/landing-lights[0]', 1);
		setprop('/controls/lighting/landing-lights[1]', 1);
		setprop('/controls/lighting/landing-lights[2]', 1);
		magicAI_pause();return;
	}
	settimer(magicAI_landing, 1);
}
var magicAI_pause = func {
	if (getprop('/position/altitude-agl-ft') < 400) {
		props.globals.getNode('/sim/freeze/clock').setBoolValue(1);
		props.globals.getNode('/sim/freeze/master').setBoolValue(1);
		magicAI_aftermath();return;
	}
	settimer(magicAI_pause, 1);
}
var magicAI_aftermath = func {
	if (getprop('/gear/gear/wow') and getprop('/velocities/airspeed-kt') < 50) {
		for (i = 0; i < 12; i += 1) {
			setprop('/controls/engines/engine['~i~']/throttle', 0);
			setprop('/controls/engines/engine['~i~']/reverser', 0);
		}
		setprop('/controls/flight/flaps', 0);
		setprop('/controls/flight/speedbrake-lever', 0);
		setprop('/controls/flight/elevator', 0);
		setprop('/controls/flight/elevator-trim', 0);
		return;
	}
	settimer(magicAI_aftermath, 1);
}

if (getprop('/sim/magicAI') == 1) {
	settimer(func(){
		setprop('/autopilot/pausemgr-dist', -1);
		magicAI_start();
	}, 3);
}

