setprop('/command/screenshot', '');
var screenshot_command = func {
	if ('capture' == getprop('/command/screenshot')){
		fgcommand('screen-capture');
		setprop('/command/screenshot', '');
	}
	settimer(screenshot_command, 0);
}
_setlistener("/sim/signals/nasal-dir-initialized", screenshot_command);

