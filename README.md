fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

## fglaunch

Start FlightGear with the aircraft specified.

Synopsis: `fglaunch [-f flight_plan [-p pause_distance_nmi]] [-a aircraft] [-- Other_Arguments...]`

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20nmi  
  You can specify `pause_distance_nmi` to set a distance other than 20nmi for Pause Manager.

## route2kml

Convert FlightGear route to KML format, so as to display the route in [Marble](http://marble.kde.org).

Synopsis: `route2kml INPUT_FILE [OUTPUT_FILE]`:

* `OUTPUT_FILE` will be set as `INPUT_FILE.kml` if not specified.

## fgtools.nas

This Nasal script includes the following features:

* Pause Manager
* Automatically activate Route Manager and ILS Frequency

Copy this file to `$FG_ROOT/Nasal` to make it work.

## Useful Unit Conversions

Length:

	1 feet = 0.3048 m    1 m = 3.2808 feet
	1 mile = 1.6093 km   1 km = 0.6214 mile
	1 nmi = 1.852 km     1 km = 0.5400 nmi
	1 mile = 0.869 nmi   1 nmi = 1.1508 mile

Mass:

	1 lbs = 0.4536 kg    1 kg = 2.2046 lbs
	1 lbs = 1 pound

## Fix Slow FPS For Boeing Airliners

Open `$FG_ROOT/Nasal/canvas/map/navdisplay.styles`.

Find out code like follows:

	{ name:'RTE', isMapStructure:1, update_on:['toggle_range','toggle_display_mode'],
		predicate: func(nd, layer) {
			var visible= (nd.in_mode('toggle_display_mode', ['MAP','PLAN']));
			layer.group.setVisible( visible );
			if (visible)
				layer.update();
		}, # end of layer update predicate
		'z-index': 1,
	}, # end of route layer

Then comment out the code above.

## Remap Arrow Keys

Use [AutoHotkey](https://www.autohotkey.com/) to remap arrow keys for operating throttle and rudder instead of aileron and elevator. Useful for keyboards without keys for operating rudder.

	#IfWinActive ahk_exe fgfs.exe
	*Up::ControlSend,,{Blind}{PgUp Down}
	*Up Up::ControlSend,,{Blind}{PgUp Up}
	*Down::ControlSend,,{Blind}{PgDn Down}
	*Down Up::ControlSend,,{Blind}{PgDn Up}
	*Left::ControlSend,,{Blind}{Insert Down}
	*Left Up::ControlSend,,{Blind}{Insert Up}
	*Right::ControlSend,,{Blind}{Enter Down}
	*Right Up::ControlSend,,{Blind}{Enter Up}
	#if

## Misceallanous

Set maximum FPS to 30 from command line:

	wget -qO/dev/null "http://localhost:port/props/sim?submit=set&frame-rate-throttle-hz=30"

Shutdown simulation from command line, by executing fgcommand "exit":

	wget -qO/dev/null "http://localhost:port/run.cgi?value=exit"

Recommened configuration for `~/.fgfsrc`

	--time-match-real
	--httpd=8080
	--prop:/autopilot/pausemgr-dist=20
	--prop:/sim/rendering/multi-sample-buffers=true
	--prop:/sim/rendering/multi-samples=8
	--nmea=socket,out,5,localhost,5500,udp

Boeing airliners should start descending when remaining distance is at around 131 nmi (including 20 nmi for landing procedure).

For PCs with touch screen, please disable touch screen from Device Manager first before running FlightGear under Windows 10. Otherwise FlightGear will be inoperatable.
