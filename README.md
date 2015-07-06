fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### FGTelnet.py

Python library for interacting with FlightGear props telnet interface.

---

#### fgtools

A useful tool to manipulate FlightGear from command line.

**Synopsis: **`fgtools command [parameter] ...`

**Available Commands:**

`fpslimit INSTANCE_NUM FPS`:

Set the maximum FPS for one or more instance(s), so as to prevent the overheat of the computer.

The value of FPS should between 15 and 70, or 0 for disable maximum FPS limit.

`launch [-f FLIGHT_PLAN [-p PAUSE_DISTANCE_NMI]] Aircraft [fgfs_options] ...`:

Start FlightGear with the aircraft specified.

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20nmi  
  You can specify `PAUSE_DISTANCE_NMI` to set a distance other than 20nmi for Pause Manager.

`pause/resume INSTANCE_NUM`:

Pause/Resume simulation.

`report INSTANCE_NUM`:

Print report of FlightGear.

`route2kml [INPUT_FILE] [OUTPUT_FILE]`:

Convert FlightGear route to KML format, so as to display the route in [Marble](http://marble.kde.org).

* If INPUT_FILE is not specified, then stdin is used.
* If OUTPUT_FILE is not specified, then stdout is used.

`shutdown INSTANCE_NUM`:

Shutdown simulation.

`soundon/soundoff INSTANCE_NUM`:

Unmute/Mute FlightGear.

`temperature`:

Show both CPU and GPU's temperature.

At present, only nVIDIA's GPU temperature will be fetched via `nvidia-smi`.

* `INSTANCE_NUM` is used to determine which FlightGear instance should be processed, specified by one of 0-9. At most 10 instances could be launched at one time.
* Multiple digits for `INSTANCE_NUM` means executing command for each instance specified, one digit for one instance.

Please copy `fgtools.nas` to `$FG_ROOT/Nasal` first, so as to get all the stuffs here work well.

---

#### 777-300.patch

Patch for [777-300](https://code.google.com/p/b773-flightgear/) and [777-300ER](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 777-300ER on startup based on the distance of the route if flight plan is specified on startup.
* Adjusted Autopilot System so as to fly polar route.
* Reduced the time of manual startup.

---

#### 757-200.patch

Patch for [757-200](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/757-200_20150111.zip).

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 757-200 on startup based on the distance of the route if flight plan is specified on startup.

---

#### 747-8.patch

Patch for [747-8f](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip) and [747-8i](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip)

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 747-8f and 747-8i on startup based on the distance of the route if flight plan is specified on startup.

---

#### B-1B.patch

Patch for [Rockwell B-1B Lancer](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.2/B-1B_20130823.zip):

* Changed the default target speed from 350 to 300.
* Changed the vertical speed under Altitude Hold mode.

---

#### B-2.patch

Patch for [Northrop B-2 Spirit](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.4/B-2_20140909.zip):

* Fixed the missing engine sound.
* Fixed the misplaced hotspots for selecting pilot mode.
* Changed the vertical speed under Altitude Hold mode.

---

#### Misceallanous

Directory __Around_The_World__ contains a series of flight plans based on Jules Verne's novel _Around the World in Eighty Days_. While the whole journey takes only 8 days by Boeing 757.

Directory __routes__ contains flight plans for long-haul and ultra long-haul flights.

