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

#### gps.xml

Added the folling information:

* Full UTC Time
* Local Time
* Total Distance
* Distance Remaining
* Flight Time
* ETE

To make the additional information work in `gps.xml`, please make sure that `fgtools.nas` exists under `$FG_ROOT/Nasal`.

Copy this file to `$FG_ROOT/gui/dialogs` to replace the original `gps.xml`.

---

#### FGMonitor

Monitor FlightGear instances from browser.

**Requirements:**

* A browser supports WebGL and [CORS](https://en.wikipedia.org/wiki/Cross-origin_resource_sharing).
* Apache/Nginx, PHP5, PHP5-cURL installed at server side.

**Installation**

* Copy `fgmonitor.nas` to `$FG_ROOT/Nasal` where FlightGear is deployed.
* Copy `fgmonitor.php` to the root folder of a web server and set it as index page.
* Enable CORS (Nginx): `add_header 'Access-Control-Allow-Origin' "example.com";`
* Enable CORS (Apache): `Header set Access-Control-Allow-Origin example.com`

**Usage**

* For PC platform, Press `r` or `space` to update data.
* For mobile platform, shake the device to update data.

---

#### Misceallanous

Directory __routes__ contains flight plans for long-haul and ultra long-haul flights.

Set maximum FPS to 30 from command line:

	wget -qO/dev/null "http://localhost:port/props/sim?submit=set&frame-rate-throttle-hz=30"

Shutdown simulation from command line, by executing fgcommand "exit":

	wget -qO/dev/null "http://localhost:port/run.cgi?value=exit"

Try the following command when you experience some strange errors during compiling FlightGear main program:

	apt-get install --no-install-recommends libxi-dev libxmu-dev

