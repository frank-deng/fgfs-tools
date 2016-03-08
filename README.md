fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### fglaunch

Start FlightGear with the aircraft specified.

Synopsis: `fglaunch [-f FLIGHT_PLAN [-p PAUSE_DISTANCE_NMI]] Aircraft [fgfs_options] ...`:

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20nmi  
  You can specify `PAUSE_DISTANCE_NMI` to set a distance other than 20nmi for Pause Manager.

---

#### route2kml

Convert FlightGear route to KML format, so as to display the route in [Marble](http://marble.kde.org).

Synopsis: `route2kml INPUT_FILE [OUTPUT_FILE]`:

* `OUTPUT_FILE` will be set as `INPUT_FILE.kml` if not specified.

---

#### fgtools.nas

This Nasal script includes the following features:

* Pause Manager
* Automatically activate Route Manager and ILS Frequency
* Convert ETE time, Flight time in seconds to HH:MM:SS format;

Copy this file to `$FG_ROOT/Nasal` to make it work.

---

#### Frequently Used Unit Conversion

Length:

	1 feet = 0.3048 m    1 m = 3.2808 feet
	1 mile = 1.6093 km   1 km = 0.6214 mile
	1 nmi = 1.852 km     1 km = 0.5400 nmi
	1 mile = 0.869 nmi   1 nmi = 1.1508 mile

Mass:

	1 lbs = 0.4536 kg    1 kg = 2.2046 lbs
	1 lbs = 1 pound

---

#### Misceallanous

Set maximum FPS to 30 from command line:

	wget -qO/dev/null "http://localhost:port/props/sim?submit=set&frame-rate-throttle-hz=30"

Shutdown simulation from command line, by executing fgcommand "exit":

	wget -qO/dev/null "http://localhost:port/run.cgi?value=exit"

Recommened configuration for `~/.fgfsrc`

	--time-match-real
	--httpd=8080
	--nmea=socket,out,5,localhost,5500,udp

